<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Instant Reward Queue Class
 * @author Manassarn M.
 */
class Instant_reward_queue_lib {

  function __construct() {
    $this->CI =& get_instance();
    $this->CI->load->model('instant_reward_queue_model');
  }

  function add($data) {
    if(!isset($data['user_id']) || !isset($data['reward_item_id']) || !isset($data['reward_machine_id'])) {
      return FALSE;
    }

    if($id = $this->CI->instant_reward_queue_model->add($data)) {
      return $id;
    }
    return FALSE;
  }

  function get($criteria, $limit = 100, $offset = 0, $sort = NULL) {
    $result = $this->CI->instant_reward_queue_model->get($criteria, $limit, $offset, $sort);
    return $result;
  }

  function get_one($criteria) {
    $result = $this->CI->instant_reward_queue_model->getOne($criteria);
    return $result;
  }

  function get_by_id($id) {
    return $this->CI->instant_reward_queue_model->getOne(array('_id' => new MongoId($id)));
  }

  function update($criteria, $data) {
    if(!$instant_reward_queue = $this->get_one($criteria)) {
      return FALSE;
    }

    $instant_reward_queue_id = get_mongo_id($instant_reward_queue);

    unset($data['_id']);

    //Pack data into $set
    if(!isset($data['$set'])) {
      $data_temp = $data;
      $data = array(
        '$set' => $data_temp
      );
    }

    $result = $this->CI->instant_reward_queue_model->update($criteria, $data);

    return $result;
  }

  function count($criteria = array()) {
    return $this->CI->instant_reward_queue_model->count($criteria);
  }
}