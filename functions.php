<?php

// Define the PHP versions of the functions.



// concat: returns all the arguments concatenated together

function concat()
{
	$str = '';
	foreach ( func_get_args() as $arg )
	{
		$str .= $arg;
	}
	return $str;
}



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
	require 'datalookup.php';
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



// strenc: converts a string into a numeric representation

function strenc( $str )
{
	$len = strlen( $str );
	$enc = '';
	for ( $i = 0; $i < $len; $i++ )
	{
		$chr = ord( substr( $str, $i, 1 ) ) - 32;
		if ( $chr >= 0 && $chr <= 94 )
		{
			$enc .= sprintf( '%02u', $chr );
		}
	}
	return $enc;
}



// substr: given a string, start position and length, returns a substring

// Uses built-in PHP function.

