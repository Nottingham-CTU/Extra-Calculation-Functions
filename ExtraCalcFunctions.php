<?php

namespace Nottingham\ExtraCalcFunctions;

class ExtraCalcFunctions extends \ExternalModules\AbstractExternalModule
{

	public function redcap_every_page_before_render()
	{
		\LogicParser::$allowedFunctions[ 'concat' ] = true;
		\LogicParser::$allowedFunctions[ 'ifnull' ] = true;
		\LogicParser::$allowedFunctions[ 'substr' ] = true;

		require 'functions.php';
	}



	public function redcap_every_page_top()
	{

?>
<script type="text/javascript" src="<?php echo $this->getUrl( 'functions_js.php' ); ?>"></script>
<?php

	}

}
