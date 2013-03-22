<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Reward Machine Class
 * @author Manassarn M.
 */
class Reward_machine_lib {

  function __construct() {
    $this->CI =& get_instance();
    $this->CI->load->model('reward_machine_model');
  }

  function add($data) {
    if($id = $this->CI->reward_machine_model->add($data)) {
      return $id;
    }
    return FALSE;
  }

  function get($criteria, $limit = 100, $offset = 0, $sort = NULL) {
    $result = $this->CI->reward_machine_model->get($criteria, $limit, $offset, $sort);
    return $result;
  }

  function get_one($criteria) {
    $result = $this->CI->reward_machine_model->getOne($criteria);
    return $result;
  }

  function get_by_id($id) {
    return $this->CI->reward_machine_model->getOne(array('_id' => new MongoId($id)));
  }

  function update($criteria, $data) {
    if(!$reward_machine = $this->get_one($criteria)) {
      return FALSE;
    }

    $reward_machine_id = get_mongo_id($reward_machine);

    unset($data['_id']);

    //Pack data into $set
    if(!isset($data['$set'])) {
      $data_temp = $data;
      $data = array(
        '$set' => $data_temp
      );
    }

    $result = $this->CI->reward_machine_model->update($criteria, $data);

    return $result;
  }

  function count($criteria = array()) {
    return $this->CI->reward_machine_model->count($criteria);
  }
}