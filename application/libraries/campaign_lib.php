<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Campaign Library
 * 
 * @author Manassarn M.
 */
class Campaign_lib {
	private $CI;
	function __construct() {
        $this->CI =& get_instance();
    }

    /**
     * Validate date range
     * @return TRUE if $from is before, or the same as $to
     * @param $from
     * @param $to
     * @author Manassarn M.
     */
    function validate_date_range($from = NULL, $to = NULL){
    	$from_time = strtotime($from);
    	$to_time = strtotime($to);
    	if($from_time === FALSE || $to_time === FALSE){
    		return FALSE;
    	} else { 
	    	return ($from_time <= $to_time);
	    }
    }

}
