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

if ( $module->getProjectSetting( 'custom-data-lookup-enable' ) )
{
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
	if ( $lookupIndex >= 0 )
	{
		$lookupProject = $module->getProjectSetting( 'custom-data-lookup-project' )[ $lookupIndex ];
		$lookupFilter = $module->getProjectSetting( 'custom-data-lookup-filter' )[ $lookupIndex ];
		$lookupField = $module->getProjectSetting( 'custom-data-lookup-field' )[ $lookupIndex ];

		if ( $lookupProject == '' )
		{
			$lookupProject = $module->getProjectId();
		}

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

		try
		{
			$lookupResult = json_decode( REDCap::getData( [ 'project_id' => $lookupProject,
			                                                'return_format' => 'json',
			                                                'filterLogic' => $lookupFilter,
			                                                'exportDataAccessGroups' => true,
			                                                'exportSurveyFields' => true ] ), true );
			if ( count( $lookupResult ) > 0 )
			{
				$result = $lookupResult[0][$lookupField];
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
	echo json_encode( $result );
}
