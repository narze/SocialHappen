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

    if($result && count($result > 0)){
      $result = array_map(function($model){
        $model['_id'] = '' . $model['_id'];

        return $model;
      }, $result);
    }

    return $result;
  }

  function get_one($criteria) {
    $result = $this->CI->branch_model->getOne($criteria);

    if($result){
      $result['_id'] = '' . $result['_id'];
    }

    return $result;
  }

  function update($criteria, $data) {
    if(!$branch = $this->get_one($criteria)) {
      return FALSE;
    }

    $branch_id = $criteria['_id'];

    unset($data['_id']);

    //Pack data into $set
    if(!isset($data['$set'])) {
      $data_temp = $data;
      $data = array(
        '$set' => $data_temp
      );
    }

    $result = $this->CI->branch_model->update($criteria, $data);

    $this->update_challenges($branch_id);

    return $result;
  }

  function remove($criteria) {
    $result = $this->CI->branch_model->delete($criteria);
    // echo '<hr>';
    // echo "remove ".count($result)." branches<br>";

    if($result && count($result) > 0){
      foreach ($result as $branch) {
        $this->update_challenges($branch['_id'] . '');
      }
    }

    return $result;
  }

  function update_challenges($branch_id){
    $this->CI->load->library('challenge_lib');

    // echo "update branch: " . $branch_id . '<br>';

    $criteria = array(
      '$or' => array(
        array('all_branch' => TRUE),
        array('branches' => '' . $branch_id)
      )
    );

    $challenges = $this->CI->challenge_lib->get($criteria, 10000);

    // echo 'got '.count($challenges).' challenge to update<br>';

    if($challenges && count($challenges) > 0){
      foreach ($challenges as $challenge) {
        $this->CI->challenge_lib->generate_locations('' . $challenge['_id']);
      }
    }
  }
}