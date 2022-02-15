<?php

namespace Nottingham\ExtraCalcFunctions;

class ExtraCalcFunctions extends \ExternalModules\AbstractExternalModule
{

	public function redcap_module_project_disable( $version, $project_id )
	{
		$this->removeProjectSetting( 'calc-values-auto-update-ts' );
	}



	public function redcap_every_page_before_render( $project_id )
	{
		// Instruct the logic parser to allow the extra functions.
		\LogicParser::$allowedFunctions[ 'datalookup' ] = true;
		\LogicParser::$allowedFunctions[ 'ifenum' ] = true;
		\LogicParser::$allowedFunctions[ 'ifnull' ] = true;
		\LogicParser::$allowedFunctions[ 'randomnumber' ] = true;

		// Define the PHP versions of the functions.
		self::$module = $this;
		require 'functions.php';

		// If auto-updating of calculated values is enabled, do this if it has not been done in the
		// last 15 minutes.
		if ( $project_id !== null &&
		     $this->getProjectSetting( 'calc-values-auto-update' ) &&
		     ( $this->getProjectSetting( 'calc-values-auto-update-ts' ) == null ||
		       $this->getProjectSetting( 'calc-values-auto-update-ts' ) + 900 < time() ) )
		{
			$this->setProjectSetting( 'calc-values-auto-update-ts', time() );
			$oldAction = null;
			if ( isset( $_POST['action'] ) )
			{
				$oldAction = $_POST['action'];
			}
			$_POST['action'] = 'fixCalcs';
			$dq = new \DataQuality();
			$dq->executeRule( 'pd-10', '' );
			if ( $oldAction === null )
			{
				unset( $_POST['action'] );
			}
			else
			{
				$_POST['action'] = $oldAction;
			}
		}
	}



	public function redcap_every_page_top()
	{

		// Include the JavaScript versions of the functions on every page.

?>
<script type="text/javascript" src="<?php echo $this->getUrl( 'functions_js.php' ); ?>"></script>
<?php

	}



	// Module settings validation.

	public function validateSettings( $settings )
	{
		$errMsg = '';

		if ( $this->getProjectID() === null )
		{
			return null;
		}

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
				}
			}
		}

		if ( $errMsg != '' )
		{
			return "Your configuration contains errors:$errMsg";
		}

		if ( ! $settings['calc-values-auto-update'] )
		{
			$this->removeProjectSetting( 'calc-values-auto-update-ts' );
		}

		return null;
	}



	public static $module = null;

}
