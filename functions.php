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



// substr: given a string, start position and length, returns a substring

// Uses built-in PHP function.

