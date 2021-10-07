<?php

namespace Nottingham\ExtraCalcFunctions;

class ExtraCalcFunctions extends \ExternalModules\AbstractExternalModule
{

	// As the REDCap built-in module configuration only contains options for administrators, hide
	// this configuration from all non-administrators.
	function redcap_module_configure_button_display( $project_id )
	{
		return $this->framework->getUser()->isSuperUser() ? true : null;
	}



	public function redcap_every_page_before_render()
	{
		\LogicParser::$allowedFunctions[ 'datalookup' ] = true;
		\LogicParser::$allowedFunctions[ 'ifenum' ] = true;
		\LogicParser::$allowedFunctions[ 'ifnull' ] = true;
		\LogicParser::$allowedFunctions[ 'randomnumber' ] = true;

		self::$module = $this;
		require 'functions.php';
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

		if ( $settings['custom-data-lookup-enable'] &&
		     count( $settings['custom-data-lookup'] ) == 0 )
		{
			$errMsg .= "\n- At least 1 custom data lookup must be defined";
		}

		if ( $errMsg != '' )
		{
			return "Your configuration contains errors:$errMsg";
		}
		return null;
	}



	public static $module = null;

}
