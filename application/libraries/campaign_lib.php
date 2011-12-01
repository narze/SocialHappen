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
                    if(isset($campaign['campaign_start_timestamp']) && isset($campaign['campaign_end_timestamp'])){
                        $start_time = strtotime($campaign['campaign_start_timestamp']);
                        $end_time = strtotime($campaign['campaign_end_timestamp']);
                        if(($start_time <= $from_time && $from_time <= $end_time) || ($start_time <= $to_time && $to_time <= $end_time) || ($from_time <= $start_time && $end_time <= $to_time)){
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
     * @return 1. FALSE if no campaign found,
     *      2. if that app is in campaign : array('in_campaign' => TRUE, 'campaign_id' => [campaign_id], 'campaign_end_message' => NULL)
     *      3. otherwise : array('in_campaign' => FALSE, 'campaign_id' => NULL, 'campaign_end_message' => [last campaign_end_message])
     * @param array $campaigns
     * @author Manassarn M.
     */
    function api_request_current_campaign_in_campaigns($campaigns = NULL){
        if(!is_array($campaigns)){
            return FALSE;
        } else {
            $now = strtotime('now');
            $recent_end_time = 0;
            $recent_end_message = $this->default_campaign_end_message;
            foreach($campaigns as $campaign){
                if(isset($campaign['campaign_start_timestamp']) && isset($campaign['campaign_end_timestamp'])){
                    $start_time = strtotime($campaign['campaign_start_timestamp']);
                    $end_time = strtotime($campaign['campaign_end_timestamp']);
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

    /**
     * Get current campaign by app_install_id
     * @param $app_install_id
     * @return 1. FALSE if no campaign found,
     *      2. if that app is in campaign : array('in_campaign' => TRUE, 'campaign_id' => [campaign_id], 'campaign_end_message' => NULL)
     *      3. otherwise : array('in_campaign' => FALSE, 'campaign_id' => NULL, 'campaign_end_message' => [last campaign_end_message])
     * @author Manassarn M.
     */
    function get_current_campaign_by_app_install_id($app_install_id){
        $this->CI->load->model('campaign_model','campaign');
        $campaigns = $this->CI->campaign->get_app_campaigns_by_app_install_id_ordered($app_install_id, 'campaign_start_timestamp desc');
        return $this->api_request_current_campaign_in_campaigns($campaigns);
    }

    /**
     * Convert campaign time
     * @param array $campaign
     * @param $minute_offset
     * @author Manassarn M.
     */
    function convert_campaign_time($campaign = NULL, $minute_offset = NULL){
        if(isset($campaign['campaign_start_timestamp']) && isset($campaign['campaign_end_timestamp'])){
            $this->CI->load->library('timezone_lib');
            $campaign['campaign_start_timestamp'] = $this->CI->timezone_lib->convert_time($campaign['campaign_start_timestamp'], $minute_offset);
            $campaign['campaign_end_timestamp'] = $this->CI->timezone_lib->convert_time($campaign['campaign_end_timestamp'], $minute_offset);
        }
        return $campaign;
    }

    /** 
     * Convert array of campaigns' time
     * @param array $campaigns
     * @param $minute_offset
     * @author Manassarn M.
     */
    function convert_campaign_time_array($campaigns = array(), $minute_offset = NULL){
        foreach($campaigns as &$campaign){
            $campaign = $this->convert_campaign_time($campaign, $minute_offset);
        }
        unset($campaign);
        return $campaigns;
    }
}
