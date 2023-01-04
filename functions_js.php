<?php

// Output the JavaScript versions of the functions.
// A PHP file is used to output the functions instead of a static JavaScript file, in order to avoid
// bugs in the module framework when getting the URL of a static file.


// Override REDCap caching headers, to allow caching.
// Return a 304 status if JavaScript unchanged from cached version.
header( 'Pragma: ' );
header( 'Expires: ' );
header( 'Cache-Control: max-age=604800' );
header( 'Content-Type: application/javascript' );

$etag = substr( sha1_file( __FILE__ ), 0, 12 );
if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag )
{
	header( 'HTTP/1.1 304 Not Modified' );
	exit;
}

// Output JavaScript.
header( 'ETag: ' . $etag );
$file = fopen( __FILE__, 'r' );
fseek( $file, __COMPILER_HALT_OFFSET__ );
echo str_replace( 'datalookup.php', $module->getUrl( 'datalookup.php' ),
             str_replace( 'loglookup.php', $module->getUrl( 'loglookup.php' ),
                          preg_replace( "!\n{2,}!", "\n",
                                        preg_replace( "!\n//[^\n]*\n!", "\n",
                                                      stream_get_contents( $file ) ) ) ) );
fclose( $file );

// End PHP
__halt_compiler();



// *** Begin JavaScript ***



// datalookup: retrieve data from any REDCap project

datalookup = (function()
{
	var luCache = {}
	return function ()
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
		luArgs = JSON.stringify( luArgs )
		if ( luCache[ luName ] == undefined )
		{
			luCache[ luName ] = {}
		}
		if ( luCache[ luName ][ luArgs ] == undefined )
		{
			luCache[ luName ][ luArgs ] = ''
			$.ajax( { url : 'datalookup.php',
			          method : 'POST', headers : { 'X-RC-ECF-Req' : '1' },
			          dataType : 'json', data : { name : luName, args : luArgs },
			          success : function ( result )
			          {
			            luCache[ luName ][ luArgs ] = result
			            calculate()
			          }
			        } )
			return ''
		}
		return luCache[ luName ][ luArgs ]
	}
})()



// ifenum (if-enumerated): return the corresponding result for the first matching value

function ifenum()
{
	if ( arguments.length < 2 || arguments.length % 2 != 0 )
	{
		return ''
	}
	var comparator = arguments[0]
	var defaultVal = arguments[1]
	for ( var i = 2; i < arguments.length; i += 2 )
	{
		if ( comparator == arguments[i] )
		{
			return arguments[i+1]
		}
	}
	return defaultVal
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



// loglookup: get data from the project log

loglookup = (function()
{
	var luCache = {}
	return function ( type = '', field = '', record = '', event = '', instance = '' )
	{
		if ( type == '' || field == '' )
		{
			return ''
		}
		var luArgs = []
		for ( var i = 0; i < arguments.length; i++ )
		{
			luArgs.push( arguments[i] )
		}
		luArgs = JSON.stringify( luArgs )
		if ( luCache[ luArgs ] == undefined )
		{
			luCache[ luArgs ] = ''
			$.ajax( { url : 'loglookup.php',
			          method : 'POST', headers : { 'X-RC-ECF-Req' : '1' },
			          dataType : 'json', data : { type : type, field : field, record : record,
			                                      event : event, instance : instance },
			          success : function ( result )
			          {
			            luCache[ luArgs ] = result
			            calculate()
			          }
			        } )
			return ''
		}
		return luCache[ luArgs ]
	}
})()



// makedate: make a date value from year/month/day components

function makedate( fmt = '', y = '', m = '', d = '' )
{
	if ( ! /^[0-9]+$/.test( y ) || ! /^((0?[1-9])|(1[012]))$/.test( m ) ||
	     ! /^((0?[1-9])|([12][0-9])|(3[01]))$/.test( d ) ||
	     ( d == 31 && [2,4,6,9,11].includes( Number( m ) ) ) || ( d == 30 && m == 2 ) ||
	     ( d == 29 && m == 2 && ( y % 4 != 0 || ( y % 100 == 0 && y % 400 != 0 ) ) ) )
	{
		return ''
	}
	if ( fmt.toLowerCase() == 'dmy' )
	{
		return ( (''+d).length == 1 ? '0' : '' ) + d + '-' +
		       ( (''+m).length == 1 ? '0' : '' ) + m + '-' + y
	}
	if ( fmt.toLowerCase() == 'mdy' )
	{
		return ( (''+m).length == 1 ? '0' : '' ) + m + '-' +
		       ( (''+d).length == 1 ? '0' : '' ) + d + '-' + y
	}
	return '' + y + '-' +
	       ( (''+m).length == 1 ? '0' : '' ) + m + '-' + ( (''+d).length == 1 ? '0' : '' ) + d
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



// sysvar: return the value of the specified system variable

function sysvar( name )
{
	if ( arguments.length == 2 && Array.isArray( arguments[1] ) )
	{
		var vars = arguments[1]
		for ( var i = 0; i < vars.length; i++ )
		{
			if ( vars[i].n == name )
			{
				return vars[i].v
			}
		}
	}
	return ''
}
