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
echo str_replace( 'datalookup.php', $module->getUrl( 'datalookup.php' ),
                   preg_replace( "!\n{2,}!", "\n",
                                 preg_replace( "!\n//[^\n]*\n!", "\n",
                                               stream_get_contents( $file ) ) ) );
fclose( $file );

// End PHP
__halt_compiler();



// *** Begin JavaScript ***



// datalookup: retrieve data from any REDCap project

function datalookup()
{
	if ( arguments.length < 1 )
	{
		return ''
	}
	var luName = arguments[0]
	var luArgs = []
	for ( var i = 1; i < arguments.length; i++ )
	{
		luArgs.push( arguments[i] )
	}
	var luResult = ''
	$.ajax( { url : 'datalookup.php',
	          method : 'POST',
	          data : { name : luName,
	                   args : JSON.stringify(luArgs) },
	          headers : { 'X-RC-ECF-Req' : '1' },
	          dataType : 'json',
	          success : function ( result )
	          {
	            luResult = result
	          },
	          async : false,
	          timeout : 10000
	        } )
	return luResult
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



// randomnumber: generate a secure random number between 0 and 1

function randomnumber()
{
	if ( crypto.getRandomValues )
	{
		var num = new Uint32Array( 1 )
		crypto.getRandomValues( num )
		return num[0] * Math.pow( 2, -32 )
	}
	return Math.random()
}
