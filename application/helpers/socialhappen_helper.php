<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * SocialHappen Helper
 * @author Manassarn M.
 */
if ( ! function_exists('issetor'))
{	
	/**
	 * Return $var if it is set, otherwise return $default
	 * @param &$var
	 * @param $default
	 * @author Manassarn M.
	 */
	function issetor(&$var, $default = FALSE) {
		return isset($var) ? $var : $default;
	}
}

if ( ! function_exists('imgsize'))
{
	/**
	 * Return image url in specified $size
	 * @param $url
	 * @param $size
	 */
	function imgsize($url = NULL, $size = NULL) {
		if(strpos($url, 'graph.facebook.com') === FALSE){
			if($size == 'square'){
				$size = 'q';
			} else if($size == 'small'){
				$size = 't';
			} else if($size == 'normal'){
				$size = 's';
			} else if($size == 'large'){
				$size = 'n';
			} else if($size == 'original'){
				$size = 'o';
			} else {
				return $url;
			}
			return preg_replace('/(\S+)_\w(\.(jpg|gif|png))/i','${1}_'.$size.'${2}',$url);
		} else {
			return "{$url}?type={$size}";
		}
	}
}

if(!function_exists('array_unique_recursive'))
{
	function array_unique_recursive($array){
		foreach($array as $key => $value){
			if(is_array($value)) {
				$array[$key] = array_unique_recursive($array[$key]);
				$array[$key] = array_unique_value($array[$key]);
			}
		}
		return $array;
	}
}

if(!function_exists('array_unique_value'))
{
	function array_unique_value($array){
		$return = array();
		foreach ($array as $key => $value){
			if(!is_numeric($key) || array_search($value, $return) === FALSE){
				$return[$key] = $value;
			} 
		}
		return $return;
    }
}

if(!function_exists('arenotempty'))
{
	function arenotempty($array,$check){
		if(!is_array($array) || !is_array($check)){
			return FALSE;
		}
		foreach ($check as $one){
			if(empty($array[$one])){
				return FALSE;
			} 
		}
		return TRUE;
    }
}

if(!function_exists('obj2array'))
{
	function obj2array($object){
		if(!$object){
			return FALSE;
		}
		return json_decode(json_encode($object), TRUE);
	}
}

if(!function_exists('filter_array'))
{
 	function filter_array($data = array(), $filter = array(), $not_set_if_not_isset = FALSE){
 		$return = array();
 		if($not_set_if_not_isset){
	 		foreach($filter as $key){
	 			if(isset($data[$key])){
	 				$return[$key] = $data[$key];
	 			}
	 		}
	 	} else {
	 		foreach($filter as $key){
	 			$return[$key] = isset($data[$key]) ? $data[$key] : NULL;
	 		}
	 	}	 	
 		return $return;
 	}
}

if(!function_exists('get_mongo_id'))
{
 	function get_mongo_id($mongo_object = NULL){
 		if(!isset($mongo_object['_id'])){
	 		return FALSE;
	 	}
	 	$id = $mongo_object['_id'];
		return $id->{'$id'};
 	}
}

if(!function_exists('allnotempty'))
{
 	function allnotempty($array = NULL){
 		if(!is_array($array)){
 			return isset($array);
 		} else {
 			$key = array_keys($array);
 			$size = sizeOf($key);
    		for ($i=0; $i<$size; $i++){
    			if(!$array[$key[$i]]){
    				return FALSE;
    			}
    		}
    		return TRUE;
 		}
 	}
}

if(!function_exists('var_dump_pre'))
{
	function var_dump_pre($input = NULL){
		echo '<pre>';
		var_dump($input);
		echo '</pre>';
	}
}
/* End of file socialhappen_helper.php */
/* Location: ./system/helpers/socialhappen_helper.php */