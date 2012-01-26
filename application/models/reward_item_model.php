<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Reward_item model
 * [
 *   {
 *  	name : <item name>,
 *  	status : <draft/published/cancelled>,
 *  	type : <redeem OR random OR top_score>,
 *		start_timestamp : <timestamp to show this item>,
 *		end_timestamp : <item's expiry date>,
 *   	redeem : {
 *   		point : <point used to redeem this item,
 *   		amount : <item amount>,
 *   		amount_remain : <item amount left>, (auto set when add)
 *   	},
 *   	random : {
 *   		amount : <amount to random>,
 *   	},
 *   	top_score : {
 *   		first_place : <first place to get this item [1+]>,
 *   		last_place : <last place to get this item [>= first_place] >
 *   	},
 *   	user_list : [array of user_ids who got this item]
 *   }
 * ]
 */
 class Reward_item_model extends CI_Model {
	
	/**
	 * Connect to mongodb
	 * @author Manassarn M.
	 */
	function __construct(){
		parent::__construct();
		$this->load->helper('mongodb');
		$this->reward_item = sh_mongodb_load( array(
			'database' => 'socialhappen',
			'collection' => 'reward_item'
		));
	}
	
	/** 
	 * Drop reward collection
	 * @author Manassarn M.
	 */
	function drop_collection(){
		return $this->reward_item->drop();
	}
	
	/**
	 * Create index for reward collection
	 * @author Manassarn M.
	 */
	function create_index(){
		return $this->reward_item->ensureIndex(array('reward'=>1));
	}
	
	/**
	 * Count all reward
	 * @author Manassarn M.
	 */
	function count_all(){
		return $this->reward_item->count();
	}
	
	/**
	 * Get reward item by reward_item_id
	 * @param $campaign_id
	 * @author Manassarn M.
	 */
	function get_by_reward_item_id($reward_item_id = NULL){
		$result = $this->reward_item->findOne(array('_id' => new MongoId($reward_item_id)));
		$result = obj2array($result);
		return $result;
	}
	
	/**
	 * Get reward item
	 * @param $criteria
	 * @author Manassarn M.
	 */
	function get($criteria = NULL){
		if(isset($criteria['criteria_id'])){
			$criteria['criteria_id'] = (int) $criteria['criteria_id'];
		}
		$result = $this->reward_item->find($criteria);
		$result = cursor2array($result);
		return $result;
	}
	
	/**
	 * Add reward by campaign_id
	 * @param $reward_item = array(
	 *		'name' => <name>,
	 *		'status' => <draft/published/cancelled>,
	 *		'type' => <redeem OR random OR top_score>
	 *		'redeem' => array(
	 *	 		'point' => <point required to redeem this item>,
	 *	 		'amount' => <amount of this reward>
	 *	 	),
	 *	 	'random' => array(
	 *		 	'amount' => <amount of this reward>
	 *		),
	 *		'top_score' => array(
	 *			'first_place' => <first place to get reward>,
	 *			'last_place' => <last place to get reward>
	 *		)
	 * )
	 * @author Manasssarn M.
	 */
	function add($input = array()){
		if(!issetor($input['name']) || !issetor($input['type']) || !issetor($input['start_timestamp']) || !issetor($input['end_timestamp'])|| !issetor($input['criteria_type']) || !issetor($input['criteria_id'])){
			return FALSE;
		}
		$input_type = $input['type'];
		$check_args = in_array($input_type, array('redeem', 'random', 'top_score'));
		if(!$check_args || !isset($input[$input_type])){
			return FALSE;
		}
		if(!in_array($input['status'], array('draft', 'published', 'cancelled'))){
			return FALSE;
		}
		if(!in_array($input['criteria_type'], array('page', 'app', 'campaign'))){
			return FALSE;
		}
		if($input['start_timestamp'] > $input['end_timestamp'] || $input['end_timestamp'] < time()){
			return FALSE;
		}
		if($input_type === 'redeem'){
			if(!isset($input['redeem']['point']) || !isset($input['redeem']['amount'])){
				return FALSE;
			}
			$input['redeem']['amount_remain'] = $input['redeem']['amount'];
		} else if ($input_type === 'random'){
			if(!isset($input['random']['amount'])){
				return FALSE;
			}
		} else if ($input_type === 'top_score'){
			if(!isset($input['top_score']['first_place']) || $input['top_score']['first_place'] < 1 || (isset($input['top_score']['last_place']) && $input['top_score']['first_place'] > $input['top_score']['last_place'])){
				return FALSE;
			}
		}
		$input['criteria_id'] = (int) $input['criteria_id'];
		$input['user_list'] = array();
		
		try	{
			$this->reward_item->insert($input, array('safe' => TRUE));
			return get_mongo_id($input);
		} catch(MongoCursorException $e){
			log_message('error', 'Mongo error : '. $e);
			return FALSE;
		}
	}

	function update($reward_item_id = NULL, $input = NULL){
		if(!$reward_item_id || !$input){
			return FALSE;
		}
		$update['$set'] = array();
		if(isset($input['name'])){
			$update['$set']['name'] = $input['name'];
		}
		if(isset($input['status'])){
			if(!in_array($input['status'], array('draft', 'published', 'cancelled'))){
				return FALSE;
			}
			$update['$set']['status'] = $input['status'];
		}
		if(isset($input['criteria_type'])){
			if(!in_array($input['criteria_type'], array('page', 'app', 'campaign'))){
				return FALSE;
			}
			$update['$set']['criteria_type'] = $input['criteria_type'];
		}
		if(isset($input['criteria_id'])){
			if(!issetor($input['criteria_id'])){
				return FALSE;
			}
			$update['$set']['criteria_id'] = (int) $input['criteria_id'];
		}
		if(isset($input['start_timestamp']) && isset($input['end_timestamp'])){
			if($input['end_timestamp'] < time() || ($input['start_timestamp'] > $input['end_timestamp'])){
				return FALSE;
			}
			$update['$set']['start_timestamp'] = $input['start_timestamp'];
			$update['$set']['end_timestamp'] = $input['end_timestamp'];
		}
		if(isset($input['type'])){
			$type = $input['type'];
			$update['$set'][$type] = array();
			if($type === 'redeem'){
				if(!isset($input[$type]['point']) || !isset($input[$type]['amount'])){
					return FALSE;
				}
				if(isset($input[$type]['point'])){
					$update['$set'][$type]['point'] = $input[$type]['point'];
				}
				if(isset($input[$type]['amount'])){
					$update['$set'][$type]['amount'] = $input[$type]['amount'];
				}
				if(isset($input[$type]['amount_remain'])){
					$update['$set'][$type]['amount_remain'] = $input[$type]['amount_remain'];
				}
				$update['$unset'] = array('random' => 1, 'top_score' => 1);
			} else if ($type === 'random'){
				if(!isset($input['random']['amount'])){
					return FALSE;
				}
				$update['$set'][$type]['amount'] = $input[$type]['amount'];
				$update['$unset'] = array('redeem' => 1, 'top_score' => 1);
			} else if ($type === 'top_score'){
				if(!isset($input['top_score']['first_place']) || $input['top_score']['first_place'] < 1 || (isset($input['top_score']['last_place']) && $input['top_score']['first_place'] > $input['top_score']['last_place'])){
					return FALSE;
				}
				$update['$set'][$type]['first_place'] = $input[$type]['first_place'];
				$update['$set'][$type]['last_place'] = $input[$type]['last_place'];
				$update['$unset'] = array('random' => 1, 'redeem' => 1);
			}
		}
		try	{
			return $this->reward_item->update(array('_id' => new MongoId($reward_item_id)),
				$update, array('safe' => TRUE));
		} catch(MongoCursorException $e){
			log_message('error', 'Mongo error : '. $e);
			return FALSE;
		}
	}
  
  /**
   * Remove reward item
   * @param reward_item_id
   * 
   * @return result boolean
   * 
   * @author Metwara Narksook
   */
  function remove($reward_item_id = NULL){
    $check_args = isset($reward_item_id);
    if($check_args){
    	try	{
			$result = $this->reward_item
                  ->remove(array('_id' => new MongoId($reward_item_id)), 
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
    } else {
      return FALSE;
    }
  }
}

/* End of file reward_model.php */
/* Location: ./application/models/reward_model.php */