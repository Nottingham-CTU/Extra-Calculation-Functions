<?php

namespace Nottingham\ExtraCalcFunctions;

class ExtraCalcFunctions extends \ExternalModules\AbstractExternalModule
{

	public function redcap_every_page_before_render()
	{
		\LogicParser::$allowedFunctions[ 'concat' ] = true;
		\LogicParser::$allowedFunctions[ 'datalookup' ] = true;
		\LogicParser::$allowedFunctions[ 'ifnull' ] = true;
		\LogicParser::$allowedFunctions[ 'randomnumber' ] = true;
		\LogicParser::$allowedFunctions[ 'strenc' ] = true;
		\LogicParser::$allowedFunctions[ 'substr' ] = true;

		require 'functions.php';
	}



	public function redcap_every_page_top()
	{

		// Include the JavaScript versions of the functions on every page.

?>
<script type="text/javascript" src="<?php echo $this->getUrl( 'functions_js.php' ); ?>"></script>
<?php


		// Amend the list of action tags to include the @STRENC tag, which denotes a calculated
		// field as holding an encoded string value.
		if ( substr( PAGE_FULL, strlen( APP_PATH_WEBROOT ), 26 ) == 'Design/online_designer.php' )
		{
?>
<script type="text/javascript">
$(function()
{
  var vActionTagPopup = actionTagExplainPopup
  var vMakeRow = function( tag, desc, position, insertAfter = false )
  {
    var vRow = $( '<tr>' + $('tr:has(td.nowrap:contains("' + position + '")):eq(0)').html() + '</tr>' )
    vRow.find('td:eq(1)').html( tag )
    vRow.find('td:eq(2)').html( desc )
    vRow.find('button').attr('onclick', vRow.find('button').attr('onclick').replace(position,tag))
    if ( insertAfter )
    {
      $('tr:has(td.nowrap:contains("' + position + '")):eq(0)').after( vRow )
    }
    else
    {
      $('tr:has(td.nowrap:contains("' + position + '")):eq(0)').before( vRow )
    }
  }
  actionTagExplainPopup = function( hideBtns )
  {
    vActionTagPopup( hideBtns )
    var vCheckTagsPopup = setInterval( function()
    {
      if ( $('div[aria-describedby="action_tag_explain_popup"]').length == 0 )
      {
        return
      }
      clearInterval( vCheckTagsPopup )
      vMakeRow( '@STRENC', 'Tag a calculated field as holding an encoded string value. The ' +
                           'encoded value will be decoded and displayed on forms and surveys.',
                           '@TODAY' )
    }, 100 )
  }
})
</script>
<?php
		}

	}



	// Provide the features on data entry forms (not surveys).

	public function redcap_data_entry_form_top( $project_id, $record=null, $instrument, $event_id,
	                                            $group_id=null, $repeat_instance=1 )
	{
		$this->decodeCalculatedStrings( $instrument );
	}



	// Provide the features on surveys.

	public function redcap_survey_page_top( $project_id, $record=null, $instrument, $event_id,
	                                        $group_id=null, $survey_hash=null, $response_id=null,
	                                        $repeat_instance=1 )
	{
		$this->decodeCalculatedStrings( $instrument );
	}



	// Output JavaScript to decode encoded string values in calculated fields.

	protected function decodeCalculatedStrings( $instrument )
	{
		// Get the calculated fields with the @STRENC action tag.
		$listEncFields = [];
		$listFormFields = \REDCap::getDataDictionary( 'array', false, true, $instrument );

		foreach ( $listFormFields as $fieldName => $infoField )
		{
			if ( $infoField[ 'field_type' ] == 'calc' &&
			     preg_match( "/(^|\\s)@STRENC(\\s|$)/", $infoField['field_annotation'] ) )
			{
				$listEncFields[] = $fieldName;
			}
		}

		if ( count( $listEncFields ) > 0 )
		{


			// Output JavaScript.
?>
<script type="text/javascript">
$(function()
{
  var vFuncUpdateStr = function ( vFieldName )
  {
    var vFieldObj = $('input[name="' + vFieldName + '"]')
    var vTextObj = vFieldObj.next()
    var vEncText = vFieldObj.val()
    var vDecText = ''
    for ( var i = 0; i < vEncText.length; i += 2 )
    {
      vDecText += String.fromCharCode( 32 + ( vEncText.substring( i, i+2 ) - 0 ) )
    }
    vTextObj.text( vDecText )
  }
  var vFields = JSON.parse('<?php echo json_encode($listEncFields); ?>')
  vFields.forEach( function( vFieldName )
  {
    var vFieldObj = $('input[name="' + vFieldName + '"]')
    vFieldObj.css('display','none')
    vFieldObj.after('<div></div>')
    vFuncUpdateStr( vFieldName )
    setInterval( function(){ vFuncUpdateStr( vFieldName ) }, 500 )
    vFieldObj.on( 'change', function() { vFuncUpdateStr( vFieldName ) } )
  })
})
</script>
<?php


		}
	}

}
