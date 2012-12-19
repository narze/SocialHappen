<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Branch_lib {

  function __construct() {
    $this->CI =& get_instance();
    $this->CI->load->model('branch_model');
  }

  function add($data) {
    if($model = $this->CI->branch_model->add($data)) {
      return $model;
    }
    return FALSE;
  }

  function get($criteria, $limit = 100) {
    $result = $this->CI->branch_model->get($criteria, $limit);
    return $result;
  }

  function get_one($criteria) {
    $result = $this->CI->branch_model->getOne($criteria);
    return $result;
  }

  function update($criteria, $data) {
    if(!$branch = $this->get_one($criteria)) {
      return FALSE;
    }

    $branch_id = get_mongo_id($branch);

    unset($data['_id']);

    //Pack data into $set
    if(!isset($data['$set'])) {
      $data_temp = $data;
      $data = array(
        '$set' => $data_temp
      );
    }

    return $this->CI->branch_model->update($criteria, $data);
  }

  function remove($criteria) {
    return $this->CI->branch_model->delete($criteria);
  }
}