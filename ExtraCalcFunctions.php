<?php

namespace Nottingham\ExtraCalcFunctions;

class ExtraCalcFunctions extends \ExternalModules\AbstractExternalModule
{

	public function redcap_module_project_disable( $version, $project_id )
	{
		$this->removeProjectSetting( 'calc-values-auto-update-ts' );
		$this->removeProjectSetting( 'calc-values-auto-update-dur' );
	}



	public function redcap_every_page_before_render( $project_id )
	{
		// Instruct the logic parser to allow the extra functions.
		\LogicParser::$allowedFunctions[ 'checkvalueoncurrentinstance' ] = true;
		\LogicParser::$allowedFunctions[ 'datalookup' ] = true;
		\LogicParser::$allowedFunctions[ 'ifenum' ] = true;
		\LogicParser::$allowedFunctions[ 'ifnull' ] = true;
		\LogicParser::$allowedFunctions[ 'loglookup' ] = true;
		\LogicParser::$allowedFunctions[ 'makedate' ] = true;
		\LogicParser::$allowedFunctions[ 'randomnumber' ] = true;
		\LogicParser::$allowedFunctions[ 'sysvar' ] = true;

		// Define the PHP versions of the functions.
		self::$module = $this;
		require 'functions.php';

		// If auto-updating of calculated values is enabled, do this if it has not been done in the
		// last 10 minutes (frequency is reduced if recalculations take a long time).
		$this->needsAutoCalc = false;
		$lastDuration = $project_id === null
		                ? 0 : ( $this->getProjectSetting( 'calc-values-auto-update-dur' ) ?? 0 );
		$autoCalcWait = $lastDuration < 60 ? 600 : ( $lastDuration * 10 );
		if ( $project_id !== null && defined( 'USERID' ) &&
		     $this->getProjectSetting( 'calc-values-auto-update' ) &&
		     ( ( defined( 'SUPER_USER' ) && SUPER_USER == 1 &&
		         isset( $_SERVER['HTTP_X_RC_ECF_AUTO_RECALC'] ) ) ||
		       $this->getProjectSetting( 'calc-values-auto-update-ts' ) == null ||
		       $this->getProjectSetting( 'calc-values-auto-update-ts' ) + $autoCalcWait < time() ) )
		{
			if ( isset( $_SERVER['HTTP_X_RC_ECF_AUTO_RECALC'] ) )
			{
				$thisIteration = $project_id === null ? 0 :
				                 ( $this->getProjectSetting( 'calc-values-auto-update-itr' ) ?? 1 );
				$splitRuns = $project_id === null ? 0 :
				              ( $this->getProjectSetting( 'calc-values-auto-update-spl' ) ?? 1 );
				if ( $lastDuration > 480 || $lastDuration === -1 )
				{
					$splitRuns++;
					$this->setProjectSetting( 'calc-values-auto-update-spl', $splitRuns );
				}
				$autoCalcStart = time();
				$this->setProjectSetting( 'calc-values-auto-update-ts', $autoCalcStart );
				$this->setProjectSetting( 'calc-values-auto-update-dur', -1 );
				$oldAction = null;
				$oldGroupID = null;
				if ( isset( $_POST['action'] ) )
				{
					$oldAction = $_POST['action'];
				}
				if ( isset( $user_rights['group_id'] ) )
				{
					$oldGroupID = $user_rights['group_id'];
				}
				$_POST['action'] = 'fixCalcs';
				$user_rights['group_id'] = null;
				$dq = new \DataQuality();
				$queryRecords = $this->query( 'SELECT DISTINCT record FROM redcap_record_list ' .
				                              'WHERE project_id = ? ORDER BY record',
				                              [ $this->getProjectId() ] );
				$listRecords = [];
				while ( $infoRecord = $queryRecords->fetch_assoc() )
				{
					$listRecords[] = $infoRecord['record'];
				}
				for ( $i = floor( ( ($thisIteration - 1) / $splitRuns ) * count( $listRecords ) );
				      $i < floor( ( $thisIteration / $splitRuns ) * count( $listRecords ) ); $i++ )
				{
					$dq->executeRule( 'pd-10', $listRecords[$i] );
				}
				if ( $oldAction === null )
				{
					unset( $_POST['action'] );
				}
				else
				{
					$_POST['action'] = $oldAction;
				}
				if ( $oldGroupID === null )
				{
					unset( $user_rights['group_id'] );
				}
				else
				{
					$user_rights['group_id'] = $oldGroupID;
				}
				header( 'Content-Type: application/json' );
				echo ( defined( 'SUPER_USER' ) && SUPER_USER == 1 )
				     ? json_encode( $dq->errorMsg ) : 'null';
				if ( $splitRuns > 1 && ( time() - $autoCalcStart ) < 90 &&
				     $lastDuration < 90 && $lastDuration !== -1 && random_int(0,1) == 1 )
				{
					$splitRuns--;
					$this->setProjectSetting( 'calc-values-auto-update-spl', $splitRuns );
				}
				$this->setProjectSetting( 'calc-values-auto-update-dur', time() - $autoCalcStart );
				$thisIteration = ( $thisIteration >= $splitRuns ) ? 1 : ( $thisIteration + 1 );
				$this->setProjectSetting( 'calc-values-auto-update-itr', $thisIteration );
				$this->exitAfterHook();
			}
			else
			{
				$this->needsAutoCalc = true;
			}
		}
	}



	public function redcap_every_page_top()
	{

		// Add the AJAX request to trigger the auto-recalculations if required.
		if ( $this->needsAutoCalc )
		{
?>
<script type="text/javascript">
$.ajax( { url : '', method : 'GET', headers : { 'X-RC-ECF-Auto-ReCalc' : '1' } } )
</script>
<?php
		}

		// Include the JavaScript versions of the functions on every page.

?>
<script type="text/javascript" src="<?php echo $this->getUrl( 'functions_js.php?NOAUTH' ), '&v=',
            preg_replace( '/^.*?([0-9.]+)$/', '$1', $this->getModuleDirectoryName() ); ?>"></script>
<?php

		// Get the system variables for use by the sysvar function.
		if ( $this->getSystemSetting( 'sysvar-enable' ) )
		{
			$numVars = count( $this->getSystemSetting( 'sysvar' ) );
			$varNames = $this->getSystemSetting( 'sysvar-name' );
			$varValues = $this->getSystemSetting( 'sysvar-value' );
			$vars = [];
			for ( $i = 0; $i < $numVars; $i++ )
			{
				$vars[] = [ 'n' => $varNames[$i], 'v' => $varValues[$i] ];
			}
			echo '<script type="text/javascript">(function(){var sv = sysvar;var vars = ';
			echo json_encode( $vars );
			echo ';sysvar = function(name){return sv(name,vars)}})()</script>', "\n";
		}


		// Amend the list of special functions (accessible from the add/edit field window in the
		// instrument designer) to include the extra functions provided by this module.
		if ( substr( PAGE_FULL, strlen( APP_PATH_WEBROOT ), 26 ) == 'Design/online_designer.php' ||
		     substr( PAGE_FULL, strlen( APP_PATH_WEBROOT ), 22 ) == 'ProjectSetup/index.php' )
		{
			$listSpecialFunctions =
				[
					[
						'checkvalueoncurrentinstance (field, value, allowNewInstance, ' .
						'maxInstances, unique)',
						'Checks the value of a field on the current instance',
						'This function is intended for use in form display logic, to control ' .
						'access to specific instances of the form based on the value of a field. ' .
						'This function may return unexpected values in other contexts. The ' .
						'function will return true if the field matches the value; or if this is ' .
						'a new instance and new instances are allowed, within the maximum, and '.
						'if unique=true the field in existing instances does not match the value.'
					],
					[
						'ifenum (comparator, default, value1, result1, value2, result2, ... )',
						'If-enumerated or switch/case function',
						'Compares the comparator with each value and returns the corresponding ' .
						'result for the first matching value. If no values match the default '.
						'result is returned.'
					],
					[
						'ifnull (value1, value2, ...)',
						'Null coalescing function',
						'Returns the first parameter supplied which is not null.'
					],
					[
						'loglookup (type, field, record, event, instance)',
						'Look up metadata from the project log',
						"The loglookup function can lookup the first or last entry in the project " .
						"log, filtered by field, record, event and instance, and return a " .
						"metadata value. Valid lookup types are: 'first-user', 'last-user', " .
						"'first-user-fullname', 'last-user-fullname', 'first-user-email', " .
						"'last-user-email', 'first-ip', 'last-ip', 'first-date', 'last-date', " .
						"'first-date-dmy', 'last-date-dmy', 'first-date-mdy', 'last-date-mdy', " .
						"'first-datetime', 'last-datetime', 'first-datetime-dmy', " .
						"'last-datetime-dmy', 'first-datetime-mdy', 'last-datetime-mdy', " .
						"'first-datetime-seconds', 'last-datetime-seconds', " .
						"'first-datetime-seconds-dmy', 'last-datetime-seconds-dmy', " .
						"'first-datetime-seconds-mdy', 'last-datetime-seconds-mdy'."
					],
					[
						'makedate (format, year, month, day)',
						'Construct date value',
						"Returns the date value for the supplied year, month and day components, " .
						"according to the specified format ('dmy', 'mdy' or 'ymd')."
					],
					[
						'randomnumber()',
						'Random number value',
						'Returns a cryptographically secure random number between 0 and 1.'
					]
				];
			if ( $this->getSystemSetting( 'sysvar-enable' ) )
			{
				$listSpecialFunctions[] =
					[
						'sysvar (varname)',
						'Get a defined system variable',
						'Returns the value for the specified system variable as defined by an ' .
						'administrator in the module system settings.'
					];
			}
			if ( $this->getProjectSetting( 'custom-data-lookup-enable' ) )
			{
				$listSpecialFunctions[] =
					[
						'datalookup (name, param1, param2, ...)',
						'Get arbitrary project data',
						'Supply a lookup name followed by parameters to invoke a data lookup ' .
						'defined by an administrator in the module project settings. The valid '.
						'lookup names and the parameters required will depend on the defined ' .
						'lookups that have been defined.'
					];
			}
			$this->provideSpecialFunctionExplain( $listSpecialFunctions );
		}

	}



	// Echo plain text to output (without Psalm taints).
	// Use only for e.g. JSON or CSV output.
	function echoText( $text )
	{
		echo array_reduce( str_split( $text ), function( $c, $i ) { return $c . $i; }, '' );
	}



	// Provide the exportable settings.
	function exportProjectSettings()
	{
		$directory = $this->getModuleDirectoryName();
		$directory = preg_replace( '/_v[0-9.]+$', '', $directory );
		$listProjects = [];
		$queryProjects = $this->query( 'SELECT project_id, app_title FROM redcap_projects', [] );
		while ( $infoProject = $queryProjects->fetch_assoc() )
		{
			$listProjects[ $infoProject['project_id'] ] = $infoProject['app_title'];
		}
		$listResult = [];
		$querySettings = $this->query( 'SELECT ems.`key`, ems.`type`, ems.`value` ' .
		                               'FROM redcap_external_module_settings ems ' .
		                               'JOIN redcap_external_modules em ' .
		                               'ON ems.external_module_id = em.external_module_id ' .
		                               'WHERE em.directory_prefix = ? AND ems.project_id = ? ' .
		                               'AND ems.`key` NOT LIKE \'calc-values-auto-update-%\'',
		                               [ $directory, $this->getProjectId() ] );
		while ( $infoSettings = $querySettings->fetch_assoc() )
		{
			if ( $infoSettings['key'] == 'custom-data-lookup-project' )
			{
				$infoSettings['value'] = json_decode( $infoSettings['value'], true );
				for ( $i = 0; $i < count( $infoSettings['value'] ); $i++ )
				{
					$infoSettings['value'][ $i ] = $listProjects[ $infoSettings['value'][ $i ] ];
				}
				$infoSettings['value'] = json_encode( $infoSettings['value'] );
			}
			$listResult[] = $infoSettings;
		}
		return $listResult;
	}



	// Output JavaScript to amend the special functions guide.

	function provideSpecialFunctionExplain( $listSpecialFunctions )
	{
		if ( empty( $listSpecialFunctions ) )
		{
			return;
		}
		$listSpecialFunctionsJS = json_encode( $listSpecialFunctions );

?>
<script type="text/javascript">
$(function()
{
  var vSpecialFunctionExplain = specialFunctionsExplanation
  var vMakeRow = function(vFuncDef, vFuncType, vFuncNotes, vInsertBefore)
  {
    var vRow = $( '<tr>' + vInsertBefore.prev('tr').html() + '</tr>' )
    vRow.find('td:eq(0)').text(vFuncDef)
    vRow.find('td:eq(1)').text(vFuncType)
    vRow.find('td:eq(2)').text(vFuncNotes)
    vInsertBefore.before(vRow)
  }
  specialFunctionsExplanation = function()
  {
    vSpecialFunctionExplain()
    var vCheckFunctionsPopup = setInterval( function()
    {
      if ( $('div[aria-describedby="special_functions_explain_popup"]').length == 0 )
      {
        return
      }
      clearInterval( vCheckFunctionsPopup )
      var vFunctionsTablePos = $('#special_functions_explain_popup table tr:has(td[colspan]):eq(0)');
      <?php echo $listSpecialFunctionsJS; ?>.forEach(function(vItem)
      {
        vMakeRow(vItem[0],vItem[1],vItem[2],vFunctionsTablePos)
      })
    }, 200 )
  }
})
</script>
<?php

	}



	// Module settings validation.

	public function validateSettings( $settings )
	{
		$errMsg = '';

		if ( $this->getProjectID() === null )
		{
			if ( $settings['sysvar-enable'] )
			{
				if ( count( $settings['sysvar'] ) == 0 )
				{
					$errMsg .= "\n- At least 1 system variable must be defined";
				}
				else
				{
					for ( $i = 0; $i < count( $settings['sysvar'] ); $i++ )
					{
						if ( $settings['sysvar-name'][$i] == '' )
						{
							$errMsg .= "\n- Name for system variable " . ($i+1) . " is missing";
						}
					}
				}
			}
		}
		else
		{
			if ( $settings['custom-data-lookup-enable'] )
			{
				if ( count( $settings['custom-data-lookup'] ) == 0 )
				{
					$errMsg .= "\n- At least 1 custom data lookup must be defined";
				}
				else
				{
					for ( $i = 0; $i < count( $settings['custom-data-lookup'] ); $i++ )
					{
						if ( $settings['custom-data-lookup-name'][$i] == '' )
						{
							$errMsg .= "\n- Name for lookup " . ($i+1) . " is missing";
						}
						if ( $settings['custom-data-lookup-field'][$i] == '' )
						{
							$errMsg .= "\n- Lookup field for lookup " . ($i+1) . " is missing";
						}
						elseif ( preg_match( '/[^A-Za-z0-9_]/',
						                     $settings['custom-data-lookup-field'][$i] ) )
						{
							$errMsg .= "\n- Lookup field for lookup " . ($i+1) . " is invalid";
						}
						if ( $settings['custom-data-lookup-filter'][$i] != '' &&
						     ! \LogicTester::isValid(
						                    str_replace( '?', "''",
						                            $settings['custom-data-lookup-filter'][$i] ) ) )
						{
							$errMsg .= "\n- Filter logic for lookup " . ($i+1) . " is invalid";
						}
					}
				}
			}
		}

		if ( $errMsg != '' )
		{
			return "Your configuration contains errors:$errMsg";
		}

		if ( $this->getProjectID() !== null && ! $settings['calc-values-auto-update'] )
		{
			$this->removeProjectSetting( 'calc-values-auto-update-ts' );
			$this->removeProjectSetting( 'calc-values-auto-update-dur' );
			$this->removeProjectSetting( 'calc-values-auto-update-itr' );
			$this->removeProjectSetting( 'calc-values-auto-update-spl' );
		}

		return null;
	}



	public static $module = null;
	private $needsAutoCalc;

}
