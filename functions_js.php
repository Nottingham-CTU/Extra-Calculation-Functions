<?php

// Output the JavaScript versions of the functions.
// A PHP file is used to output the functions instead of a static JavaScript file, in order to avoid
// bugs in the module framework when getting the URL of a static file.


// Override REDCap caching headers, to allow caching.
// Return a 304 status if icon unchanged from cached version.
header( 'Pragma: ' );
header( 'Expires: ' );
header( 'Cache-Control: max-age=2419200' );

$etag = substr( sha1_file( __FILE__ ), 0, 12 );
if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag )
{
	header( 'HTTP/1.1 304 Not Modified' );
	exit;
}

// Output JavaScript.
header( 'Content-Type: application/javascript' );
header( 'ETag: ' . $etag );
$file = fopen( __FILE__, 'r' );
fseek( $file, __COMPILER_HALT_OFFSET__ );
echo preg_replace( "!\n{2,}!", "\n",
                   preg_replace( "!\n//[^\n]*\n!", "\n", stream_get_contents( $file ) ) );
fclose( $file );

// End PHP
__halt_compiler();



// *** Begin JavaScript ***



// concat: returns all the arguments concatenated together

function concat()
{
	var str = ''
	for ( var i = 0; i < arguments.length; i++ )
	{
		str += arguments[i]
	}
	return str
}



// ifnull: return the first argument to the function which is not null

function ifnull()
{
	for ( var i = 0; i < arguments.length; i++ )
	{
		if ( arguments[i] != null && arguments[i] != undefined &&
		     arguments[i] != '' && arguments[i] != 'NaN' )
		{
			return arguments[i]
		}
	}
	return ''
}



// strenc

function strenc( str )
{
	str = '' + str
	var enc = ''
	for ( var i = 0; i < str.length; i++ )
	{
		var chr = str.charCodeAt( i ) - 32
		if ( chr >= 0 && chr <= 94 )
		{
			chr = ( chr < 10 ? '0' : '' ) + chr
			enc += chr
		}
	}
	return enc
}



// substr: given a string, start position and length, returns a substring

function substr( str, start, len )
{
	str = '' + str
	if ( start < 0 )
	{
		start = str.length + start
	}
	var end = str.length
	if ( len >= 0 )
	{
		end = start + len
	}
	else if ( len < 0 )
	{
		end = str.length + len
	}
	return str.substring( start, end )
}
