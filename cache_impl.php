<?php
/*. require_module 'standard'; .*/
/*. require_module 'extra'; .*/
class Cache{


	/**
	 *
	 * @return void
	 */
	function __construct(){
	}


	/**
	 * @param string $key
	 * 
	 * @return string[string]
	 */
	public static function getStringArray($key){
		$bRes = false;
		$vData = apc_fetch($key, $bRes);
		if ($bRes){
			return /*. (string[string]) .*/ $vData;
		}
		return  null;
	}

	/**
	 * @param string $key
	 * @param string[string] $value
	 * 
	 * @return void
	 */
	public static function putStringArray($key,$value){
		apc_store($key,$value,3600); //1 hour
	}

	/**
	 * @param string $key
	 * 
	 * @return string
	 */
	public static function getString($key){
		$bRes = false;
		$vData = apc_fetch($key, $bRes);
		if ($bRes){
			return /*. (string) .*/ $vData;
		}
		return  null;
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @param int $time_period
	 * 
	 * @return void
	 */
	public static function putString($key,$value, $time_period=3600){
		apc_store($key,$value,$time_period); //1 hour
	}
}

?>
