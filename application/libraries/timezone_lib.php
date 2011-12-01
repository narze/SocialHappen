<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Timezone Library
 * 
 * @author Manassarn M.
 */
class Timezone_lib {

	function __construct() {
        $this->CI =& get_instance();
    }

    function get_minute_offset_from_timezone($timezone = NULL){
    	try {
    		$input_timezone = new DateTimeZone($timezone);
    		return $input_timezone->getOffset(new DateTime('now')) / 60;
    	} catch (Exception $e){
    		return FALSE;
    	}
    }
}
