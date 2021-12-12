<?php

/*. require_module 'standard'; .*/
/*. pragma 'error_throws_exception' 'ErrorException'; .*/



/**
 * @access private
 *
 * @param Exception $e
 *  
 * @return void
 */
function phplint_exception_handler($e){
	error_log( "Uncaught ". $e->__toString() );
}




/**
 * @access private
 *
 * @param int $errno
 * 
 * @return string
 */
function phplint_error_level($errno){
	switch( $errno ){
		/* These can't be handled: E_ERROR E_PARSE E_CORE_ERROR
			E_CORE_WARNING E_COMPILE_WARNING E_COMPILE_ERROR */
		case E_WARNING:     return "E_WARNING";
		case E_NOTICE:      return "E_NOTICE";
		case E_STRICT:      return "E_STRICT";
		case E_USER_ERROR:  return "E_USER_ERROR";
		case E_USER_WARNING:return "E_USER_WARNING";
		case E_USER_NOTICE: return "E_USER_NOTICE";
		default:            return "UNKNOWN_ERROR_LEVEL_$errno";
	}
}


/**
 * @access private
 *
 * @param int $errno
 * @param string $message
 * @param string $filename
 * @param int $lineno
 * @param mixed $context
 *
 * @return boolean
 */
function phplint_error_handler( $errno, $message,$filename,$lineno,$context) /*. throws ErrorException .*/
{ 
	throw new ErrorException( phplint_error_level($errno)
		. ": $message in $filename:$lineno"); 
}


error_reporting(E_ALL|E_STRICT);
set_exception_handler("phplint_exception_handler");
set_error_handler("phplint_error_handler");

?>
