<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Campaign Library
 * 
 * @author Manassarn M.
 */
class Campaign_lib {
	private $CI;
    private $default_campaign_end_message = 'No campaign created';
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

    /**
     * Validate date range with campaigns
     * @return TRUE if $from & $to is not in another date ranges
     * @param $from
     * @param $to
     * @param array $campaigns (from campaign_model)
     * @author Manassarn M.
     */
    function validate_date_range_with_campaigns($from = NULL, $to = NULL, $campaigns = NULL){
        $from_time = strtotime($from);
        $to_time = strtotime($to);
        if($from_time === FALSE || $to_time === FALSE || $from_time > $to_time){
            return FALSE;
        } else {
            if(!$campaigns){
                return TRUE;
            } else if(is_array($campaigns)){
                foreach($campaigns as $campaign){
                    if(isset($campaign['campaign_start_date']) && isset($campaign['campaign_end_date'])){
                        $start_time = strtotime($campaign['campaign_start_date']);
                        $end_time = strtotime($campaign['campaign_end_date']);
                        if(($start_time <= $from_time && $from_time <= $end_time) || ($start_time <= $to_time && $to_time <= $end_time)){
                            return FALSE;
                        }
                    }
                }
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * API : Request current campaign
     * @param array $campaigns
     */
    function api_request_current_campaign_in_campaigns($campaigns = NULL){
        if(!is_array($campaigns)){
            return FALSE;
        } else {
            $now = strtotime('now');
            $recent_end_time = 0;
            $recent_end_message = $this->default_campaign_end_message;
            foreach($campaigns as $campaign){
                if(isset($campaign['campaign_start_date']) && isset($campaign['campaign_end_date'])){
                    $start_time = strtotime($campaign['campaign_start_date']);
                    $end_time = strtotime($campaign['campaign_end_date']);
                    if($start_time <= $now && $now <= $end_time){
                        return array(
                            'in_campaign' => TRUE,
                            'campaign_id' => $campaign['campaign_id'],
                            'campaign_end_message' => NULL
                        );
                    } else if ($recent_end_time < $end_time && $end_time < $now) {
                        $recent_end_time = $end_time;
                        $recent_end_message = $campaign['campaign_end_message'];
                    }
                }
            }
            return array(
                'in_campaign' => FALSE,
                'campaign_id' => NULL,
                'campaign_end_message' => $recent_end_message
            );
        }
    }
}