<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Reward model
 * [
 *   {
 *   	criteria : {
 *   		page_id : <page_id> OR app_install_id : <app_install_id> OR campaign_id : <campaign_id>
 *   	},
 *   	timestamp : <timestamp to announce reward, redeem expiration date>
 *   }]
 */
 class Reward_model extends CI_Model {
	
	/**
	 * Connect to mongodb
	 * @author Manassarn M.
	 */
	function __construct(){
		parent::__construct();
		$this->load->helper('mongodb');
		$this->reward = sh_mongodb_load( array(
			'database' => 'socialhappen',
			'collection' => 'reward'
		));
	}
	
	/** 
	 * Drop reward collection
	 * @author Manassarn M.
	 */
	function drop_collection(){
		return $this->reward->drop();
	}
	
	/**
	 * Create index for reward collection
	 * @author Manassarn M.
	 */
	function create_index(){
		return $this->reward->ensureIndex(array('criteria.campaign_id'=>1, 'criteria.app_install_id'=>1, 'criteria.page_id'=>1), array('unique' => 1));
	}
	
	/**
	 * Count all reward
	 * @author Manassarn M.
	 */
	function count_all(){
		return $this->reward->count();
	}
	
	
	/**
	 * Get reward by campaign_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_by_campaign_id($campaign_id = NULL){
		$result = $this->reward
			->findOne(array('criteria.campaign_id' => (int) $campaign_id));
		
		$result = obj2array($result);
		return $result;
	}
	
	/**
	 * Get reward by app_install_id
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function get_by_app_install_id($app_install_id = NULL){
		$result = $this->reward
			->findOne(array('criteria.app_install_id' => (int) $app_install_id));
		
		$result = obj2array($result);
		return $result;
	}
	
	/**
	 * Get reward by page_id
	 * @param $page_id
	 * @author Manassarn M.
	 */
	function get_by_page_id($page_id = NULL){
		$result = $this->reward
			->findOne(array('criteria.page_id' => (int) $page_id));
		
		$result = obj2array($result);
		return $result;
	}
	
	/**
	 * Add reward
	 * @param $input = array(campaign_id OR app_install_id OR page_id as key)
	 * )
	 * @author Manasssarn M.
	 */
	function add($input = array()){
		$check_args = (isset($input['campaign_id']) || isset($input['app_install_id']) || isset($input['page_id'])) && isset($input['timestamp']);
		if(!$check_args){
			return FALSE;
		} else {
			$reward = array(
				'criteria' => NULL,
				'timestamp' => $input['timestamp']
			);
			if(isset($input['campaign_id'])){
				$reward['criteria'] = array('campaign_id' => $input['campaign_id']);
			} else if(isset($input['app_install_id'])){
				$reward['criteria'] = array('app_install_id' => $input['app_install_id']);
			} else if(isset($input['page_id'])){
				$reward['criteria'] = array('page_id' => $input['page_id']);
			}
			try	{
				$this->reward->insert($reward, array('safe' => TRUE));
				return get_mongo_id($reward);
			} catch(MongoCursorException $e){
				log_message('error', 'Mongo error : '. $e);
				return FALSE;
			}
		}
	}

  /**
   * Remove reward
   * @param campaign_id
   * 
   * @return result bolean
   * 
   * @author Metwara Narksook
   */
  function remove_by_campaign_id($campaign_id = NULL){
    $check_args = $campaign_id;
    if($check_args){
        try	{
        	$result = $this->reward
                  ->remove(array("criteria.campaign_id" => $campaign_id), 
                  array('$atomic' => TRUE, 'safe' => TRUE));
            if($result['n'] != 0 || $result['err']){
            	return TRUE;
            } else {
            	return FALSE;
            }
		} catch(MongoCursorException $e){
			log_message('error', 'Mongo error : '. $e);
			return FALSE;
		}
    }else{
      return FALSE;
    }
  }
  
  /**
   * Remove reward
   * @param app_install_id
   * 
   * @return result bolean
   * 
   * @author Metwara Narksook
   */
  function remove_by_app_install_id($app_install_id = NULL){
    $check_args = $app_install_id;
    if($check_args){
      return $this->reward
                  ->remove(array("criteria.app_install_id" => $app_install_id), 
                  array('$atomic' => TRUE));
    }else{
      return FALSE;
    }
  }
  
  /**
   * Remove reward
   * @param page_id
   * 
   * @return result bolean
   * 
   * @author Metwara Narksook
   */
  function remove_by_page_id($page_id = NULL){
    $check_args = $page_id;
    if($check_args){
      return $this->reward
                  ->remove(array("criteria.page_id" => $page_id), 
                  array('$atomic' => TRUE));
    }else{
      return FALSE;
    }
  }
}

/* End of file reward_model.php */
/* Location: ./application/models/reward_model.php */