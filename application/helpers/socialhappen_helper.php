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
		return preg_replace('/(\S+)(\.(jpg|gif|png))/i','${1}_'.$size.'${2}',$url);
	}
}


/* End of file socialhappen_helper.php */
/* Location: ./system/helpers/socialhappen_helper.php */