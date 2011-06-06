<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * SocialHappen Helper
 * @author Manassarn M.
 */
if ( ! function_exists('issetor'))
{
	function issetor(&$var, $default = FALSE) {
		return isset($var) ? $var : $default;
	}
}
/* End of file socialhappen_helper.php */
/* Location: ./system/helpers/socialhappen_helper.php */