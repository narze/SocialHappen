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

    /**
     * Convert mysql timestamp into local time using minute offset
     * @param $time
     * @param $minute_offset
     * @author Manassarn M.
     */
    function convert_time($time = NULL, $minute_offset = NULL){
        if($minute_offset === NULL){
            return FALSE;
        }
        if($minute_offset < 0){
            $sign = ' -';
        } else {
            $sign = ' +';
        }
        return date('Y-m-d H:i:s', strtotime($time.$sign.abs($minute_offset).' minute'));
    }

    /**
     * Convert local time back into mysql timestamp using minute offset
     * @param $time
     * @param $minute_offset
     * @author Manassarn M.
     */
    function unconvert_time($time = NULL, $minute_offset = NULL){
        if($minute_offset === NULL){
            return FALSE;
        }
        if($minute_offset < 0){
            $sign = ' +';
        } else {
            $sign = ' -';
        }
        return date('Y-m-d H:i:s', strtotime($time.$sign.abs($minute_offset).' minute'));
    }
}
