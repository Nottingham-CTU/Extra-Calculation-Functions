<?php

if ( ! $doLogLookup && ! isset( $_SERVER['HTTP_X_RC_ECF_REQ'] ) )
{
	exit;
}


if ( ! $doLogLookup ) // AJAX request
{
	header( 'Content-Type: application/json' );
	$type = $_POST['type'];
	$field = $_POST['field'];
	$record = $_POST['record'];
	$event = $_POST['event'];
	$instance = $_POST['instance'];
}


$result = '';
$project = $module->getProjectId();

if ( $project != '' && $type != '' && $field != '' )
{
	// If event name is specified, convert to the event ID.
	$eventID = $event == '' ? '' : $GLOBALS['Proj']->getEventIdUsingUniqueEventName( $event );

	// Strip invalid characters from field names and escape underscores for SQL LIKE patterns.
	$field = preg_replace( '/[^A-Za-z0-9,_]/', '', $field );
	$listFieldPatterns = explode( ',', str_replace( '_', '\\_', $field ) );

	// Get the table used for logging by the project.
	$logEventTable = REDCap::getLogEventTable( $project );

	// Begin the query ...
	$query = "SELECT l.ts, l.user, u.user_firstname, u.user_lastname, u.user_email, l.ip, " .
	         "l.data_values FROM $logEventTable l LEFT JOIN redcap_user_information u " .
	         "ON l.user = u.username WHERE l.project_id = ? AND (l.event = 'INSERT' " .
	         "OR l.event = 'UPDATE') AND l.object_type = 'redcap_data'";
	$params = [ $project ];
	$query .= " AND (";
	// ... look for the field names in data_values ...
	$firstFieldPattern = true;
	foreach ( $listFieldPatterns as $fieldPattern )
	{
		if ( $firstFieldPattern )
		{
			$firstFieldPattern = false;
		}
		else
		{
			$query .= " OR ";
		}
		$query .= "l.data_values LIKE ? OR l.data_values LIKE ? OR " .
		          "l.data_values LIKE ? OR l.data_values LIKE ?";
		$params[] = $fieldPattern . ' %';
		$params[] = "%\n" . $fieldPattern . ' %';
		$params[] = $fieldPattern . '(%';
		$params[] = "%\n" . $fieldPattern . '(%';
	}
	$query .= ")";
	// ... filter by the record if specified ...
	if ( $record != '' )
	{
		$query .= " AND l.pk = ?";
		$params[] = $record;
	}
	// ... filter by the event if specified ...
	if ( $eventID != '' )
	{
		$query .= " AND l.event_id = ?";
		$params[] = $eventID;
	}
	// ... filter by the instance if specified ...
	if ( $instance != '' )
	{
		if ( $instance == 1 )
		{
			$query .= " AND l.data_values NOT LIKE '[instance = %'";
		}
		else
		{
			$query .= " AND l.data_values LIKE ?";
			$params[] = '[instance = ' . $instance . ']%';
		}
	}
	// ... and retrieve the first or last matching entry as appropriate.
	if ( substr( $type, 0, 6 ) == 'first-' )
	{
		$query .= " ORDER BY l.log_event_id LIMIT 1";
	}
	elseif ( substr( $type, 0, 5 ) == 'last-' )
	{
		$query .= " ORDER BY l.log_event_id DESC LIMIT 1";
	}
	$queryLog = $module->query( $query, $params );
	$infoResult = $queryLog->fetch_assoc();

	// Return the requested data item from the log entry.
	if ( $infoResult === null )
	{
		$result = '';
	}
	else
	{
		if ( $type == 'first-user' || $type == 'last-user' )
		{
			$result = $infoResult['user'];
		}
		elseif ( $type == 'first-user-fullname' || $type == 'last-user-fullname' )
		{
			if ( $infoResult['user'] == '[survey respondent]' )
			{
				$result = '[survey respondent]';
			}
			else
			{
				$result = $infoResult['user_firstname'] . ' ' . $infoResult['user_lastname'];
			}
		}
		elseif ( $type == 'first-user-email' || $type == 'last-user-email' )
		{
			$result = $infoResult['user_email'] ?? '';
		}
		elseif ( $type == 'first-ip' || $type == 'last-ip' )
		{
			$result = $infoResult['ip'] ?? '';
		}
		elseif ( in_array( $type, [ 'first-date', 'last-date', 'first-date-dmy', 'last-date-dmy',
		                            'first-date-mdy', 'last-date-mdy',
		                            'first-datetime', 'last-datetime',
		                            'first-datetime-dmy', 'last-datetime-dmy',
		                            'first-datetime-mdy', 'last-datetime-mdy',
		                            'first-datetime-seconds', 'last-datetime-seconds',
		                            'first-datetime-seconds-dmy', 'last-datetime-seconds-dmy',
		                            'first-datetime-seconds-mdy', 'last-datetime-seconds-mdy' ] ) )
		{
			if ( !$doLogLookup && substr( $type, -4 ) == '-dmy' )
			{
				$result = substr( $infoResult['ts'], 6, 2 ) . '-' .
				          substr( $infoResult['ts'], 4, 2 ) . '-' .
				          substr( $infoResult['ts'], 0, 4 );
			}
			elseif ( !$doLogLookup && substr( $type, -4 ) == '-mdy' )
			{
				$result = substr( $infoResult['ts'], 4, 2 ) . '-' .
				          substr( $infoResult['ts'], 6, 2 ) . '-' .
				          substr( $infoResult['ts'], 0, 4 );
			}
			else
			{
				$result = substr( $infoResult['ts'], 0, 4 ) . '-' .
				          substr( $infoResult['ts'], 4, 2 ) . '-' .
				          substr( $infoResult['ts'], 6, 2 );
			}
			if ( strpos( $type, '-datetime' ) !== false )
			{
				$result .= ' ' . substr( $infoResult['ts'], 8, 2 ) . ':' .
				           substr( $infoResult['ts'], 10, 2 );
				if ( strpos( $type, '-seconds' ) !== false )
				{
					$result .= ':' . substr( $infoResult['ts'], 12, 2 );
				}
			}
		}
	}
}


if ( $doLogLookup )
{
	return $result;
}
else
{
	$module->echoText( json_encode( $result ) );
}
