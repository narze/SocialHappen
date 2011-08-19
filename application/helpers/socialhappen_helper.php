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


/* End of file socialhappen_helper.php */
/* Location: ./system/helpers/socialhappen_helper.php */