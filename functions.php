<?php

// Define the PHP versions of the functions.



// datalookup: retrieve data from any REDCap project

function datalookup()
{
	$args = func_get_args();
	if ( count( $args ) < 1 )
	{
		return '';
	}
	$name = array_shift( $args );
	$doDataLookup = true;
	$module = \Nottingham\ExtraCalcFunctions\ExtraCalcFunctions::$module;
	return require 'datalookup.php';
}



// ifenum (if-enumerated): return the corresponding result for the first matching value

function ifenum()
{
	$args = func_get_args();
	if ( count( $args ) < 2 || count( $args ) % 2 != 0 )
	{
		return '';
	}
	$comparator = array_shift( $args );
	$default = array_shift( $args );
	for ( $i = 0; $i < count( $args ); $i += 2 )
	{
		if ( $comparator == $args[$i] )
		{
			return $args[$i+1];
		}
	}
	return $default;
}



// ifnull: return the first argument to the function which is not null

function ifnull()
{
	foreach ( func_get_args() as $arg )
	{
		if ( $arg !== null && $arg !== '' && $arg != 'NaN' )
		{
			return $arg;
		}
	}
	return '';
}



// loglookup: get data from the project log

function loglookup( $type = '', $field = '', $record = '', $event = '', $instance = '' )
{
	if ( $type == '' || $field == '' )
	{
		return '';
	}
	$doLogLookup = true;
	$module = \Nottingham\ExtraCalcFunctions\ExtraCalcFunctions::$module;
	return require 'loglookup.php';
}



// makedate: make a date value from year/month/day components

function makedate( $fmt = '', $y = '', $m = '', $d = '' )
{
	if ( ! preg_match( '/^[0-9]+$/', $y ) || ! preg_match( '/^((0?[1-9])|(1[012]))$/', $m ) ||
	     ! preg_match( '/^((0?[1-9])|([12][0-9])|(3[01]))$/', $d ) ||
	     ( $d == 31 && in_array( $m, [ 2, 4, 6, 9, 11 ], false ) ) || ( $d == 30 && $m == 2 ) ||
	     ( $d == 29 && $m == 2 && ( $y % 4 != 0 || ( $y % 100 == 0 && $y % 400 != 0 ) ) ) )
	{
		return '';
	}
	return $y . '-' .
	       ( strlen( $m ) == 1 ? '0' : '' ) . $m . '-' . ( strlen( $d ) == 1 ? '0' : '' ) . $d;
}



// randomnumber: generate a secure random number between 0 and 1

function randomnumber()
{
	return random_int( 0, ( PHP_INT_MAX - 1 ) ) / PHP_INT_MAX;
}



// sysvar: return the value of the specified system variable

function sysvar( $name = '' )
{
	$module = \Nottingham\ExtraCalcFunctions\ExtraCalcFunctions::$module;
	if ( $module->getSystemSetting( 'sysvar-enable' ) )
	{
		$numVars = count( $module->getSystemSetting( 'sysvar' ) );
		$varNames = $module->getSystemSetting( 'sysvar-name' );
		$varValues = $module->getSystemSetting( 'sysvar-value' );
		for ( $i = 0; $i < $numVars; $i++ )
		{
			if ( $varNames[$i] == $name )
			{
				return $varValues[$i];
			}
		}
	}
	return '';
}

