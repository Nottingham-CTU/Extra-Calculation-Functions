<?php

if ( ! $doDataLookup && ! isset( $_SERVER['HTTP_X_RC_ECF_REQ'] ) )
{
	exit;
}


if ( ! $doDataLookup ) // AJAX request
{
	header( 'Content-Type: application/json' );
	$name = $_POST['name'];
	$args = json_decode( $_POST['args'], true );
}


$result = '';

// Only perform a lookup if custom data lookup is enabled.
if ( $module->getProjectSetting( 'custom-data-lookup-enable' ) )
{
	// Loop through the custom data lookup settings to find the lookup name.
	$lookupIndex = -1;
	$listLookupNames = $module->getProjectSetting( 'custom-data-lookup-name' );
	for ( $i = 0; $i < count( $listLookupNames ); $i++ )
	{
		if ( $listLookupNames[$i] == $name )
		{
			$lookupIndex = $i;
			break;
		}
	}
	// If the specified lookup name was found in the settings:
	if ( $lookupIndex >= 0 )
	{
		// Get the corresponding settings.
		$lookupProject = $module->getProjectSetting( 'custom-data-lookup-project' )[ $lookupIndex ];
		$lookupFilter = $module->getProjectSetting( 'custom-data-lookup-filter' )[ $lookupIndex ];
		$lookupField = $module->getProjectSetting( 'custom-data-lookup-field' )[ $lookupIndex ];
		$lookupUseLabel = $module->getProjectSetting( 'custom-data-lookup-use-label' )[ $lookupIndex ];
		$lookupType = $module->getProjectSetting( 'custom-data-lookup-type' )[ $lookupIndex ];
		$lookupListSep = $module->getProjectSetting( 'custom-data-lookup-list-sep' )[ $lookupIndex ];
		$lookupCheckboxSplit = $module->getProjectSetting( 'custom-data-lookup-split-checkbox' )[ $lookupIndex ];

		// Allow a newline to be specified as the list separator using '\n'.
		if ( $lookupListSep == "\\n" )
		{
			$lookupListSep = "\n";
		}

		// If a project is not specified, use the current project.
		if ( $lookupProject == '' )
		{
			$lookupProject = $module->getProjectId();
		}

		// Replace '?' placeholders with the supplied arguments.
		foreach ( $args as $arg )
		{
			$pos = strpos( $lookupFilter, '?' );
			if ( $pos === false )
			{
				break;
			}
			$lookupFilter = substr_replace( $lookupFilter, "'" . addslashes( $arg ) . "'", $pos, 1 );
		}
		$lookupFilter = str_replace( '?', "''", $lookupFilter );

		// Attempt to perform the lookup.
		try
		{
			$lookupResult = json_decode( REDCap::getData( [ 'project_id' => $lookupProject,
			                                                'return_format' => 'json',
			                                                'filterLogic' => $lookupFilter,
			                                                'exportDataAccessGroups' => true,
			                                                'exportSurveyFields' => true,
									'combine_checkbox_values' => true,  // excludes unchecked items
			                                                'exportAsLabels' => $lookupUseLabel ] ),
			                             true );
			// Remove any returned records where the lookup field is empty.
			if ( count( $lookupResult ) > 0 )
			{
				foreach ( $lookupResult as $lookupResultIndex => $lookupResultItem )
				{
					if ( $lookupResultItem[$lookupField] == '' )
					{
						unset( $lookupResult[$lookupResultIndex] );
					}
				}
				$lookupResult = array_values( $lookupResult );
			}
			// Determine the total non-blank results and return a value if the total is at least 1.
			if ( ( $lookupCount = count( $lookupResult ) ) > 0 )
			{
				if ( $lookupType == 'list' )
				{
					// List each item, separated by the defined separator.
					foreach ( $lookupResult as $lookupResultItem )
					{
						if(REDCap::getFieldType($lookupField) === 'checkbox' && $lookupCheckboxSplit === true)
						{
							$cbResult =  explode(',', $lookupResultItem[$lookupField]);
							foreach($cbResult as $cbItem)
							{
								if ( $result != '' )
								{
									$result .= $lookupListSep;
								}
								$result .= $cbItem;
							}
						}
						else
						{
							if ( $result != '' )
							{
								$result .= $lookupListSep;
							}
							$result .= $lookupResultItem[$lookupField];
						}
					}
				}
				elseif ( $lookupType == 'plus' )
				{
					// Return the first item, with an indication of how many more items there are
					// (if the total is greater than 1).
					$cbCount = 0;
					if(REDCap::getFieldType($lookupField) === 'checkbox' && $lookupCheckboxSplit === true)
					{
						foreach ( $lookupResult as $lookupResultItem )
						{
							$cbResult =  explode(',', $lookupResultItem[$lookupField]);
							if($cbCount == 0)
							{
								$result = $cbResult[0];
							}
							$cbCount += count($cbResult);
						}
						$lookupCount = $cbCount;

					}
					else
					{
						$result = $lookupResult[0][$lookupField];
					}
					
					if ( $lookupCount > 1 )
					{
						$result .= ' (+' . ( $lookupCount - 1 ) . ')';
					}
				}
				elseif ( $lookupType == 'count' )
				{
					// Return only the total.
					$cbCount = 0;
					if(REDCap::getFieldType($lookupField) === 'checkbox' && $lookupCheckboxSplit === true)
					{
						foreach ( $lookupResult as $lookupResultItem )
						{
							$cbResult =  explode(',', $lookupResultItem[$lookupField]);
							$cbCount += count($cbResult);
						}
						$lookupCount = $cbCount;

					}
					
					$result = $lookupCount;					
				}
				else
				{
					// Return only the first item.
					if(REDCap::getFieldType($lookupField) === 'checkbox' && $lookupCheckboxSplit === true)
					{
						$cbResult =  explode(',', $lookupResult[0][$lookupField]);
						$result = $cbResult[0];
					}
					else
					{
						$result = $lookupResult[0][$lookupField];
					}
				}
			}
		}
		// Ignore any exceptions, as any error in finding the project, applying filter logic, etc.
		// means we return the empty string, which the $result variable has already been set to.
		catch ( Exception $e ) {}
	}
}



if ( $doDataLookup )
{
	return $result;
}
else
{              
	$module->echoText( json_encode( $result ) );
}
