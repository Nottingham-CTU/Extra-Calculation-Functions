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

