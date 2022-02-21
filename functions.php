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
		if ( $arg != null && $arg != '' && $arg != 'NaN' )
		{
			return $arg;
		}
	}
	return '';
}



// randomnumber: generate a secure random number between 0 and 1

function randomnumber()
{
	return random_int( 0, ( PHP_INT_MAX - 1 ) ) / PHP_INT_MAX;
}



// sysvar: return the value of the specified system variable

function sysvar( $name )
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

