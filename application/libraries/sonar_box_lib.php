<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Sonar_box_lib {

  function __construct() {
    $this->CI =& get_instance();
    $this->CI->load->model('sonar_box_model');
  }

  function add($data) {
    if($id = $this->CI->sonar_box_model->add($data)) {
      if(isset($data['branch_id'])) {
        $this->update_challenges_sonar_data($data['branch_id']);
      }
      return $id;
    }
    return FALSE;
  }

  function get($criteria, $limit = 100) {
    $result = $this->CI->sonar_box_model->get_all($criteria, $limit);

    if($result && count($result > 0)){
      $result = array_map(function($model){
        $model['_id'] = '' . $model['_id'];

        return $model;
      }, $result);
    }

    return $result;
  }

  function get_one($criteria) {
    $result = $this->CI->sonar_box_model->getOne($criteria);

    if($result){
      $result['_id'] = '' . $result['_id'];
    }

    return $result;
  }

  function update($criteria, $data) {
    if(!$sonar_box = $this->get_one($criteria)) {
      return FALSE;
    }

    unset($data['_id']);

    //Pack data into $set
    if(!isset($data['$set'])) {
      $data_temp = $data;
      $data = array(
        '$set' => $data_temp
      );
    }

    $result = $this->CI->sonar_box_model->update($criteria, $data);

    // Update challenges that bind to the box with old branch id
    if(isset($sonar_box['branch_id']) && $sonar_box['branch_id']) {
      $this->update_challenges_sonar_data($sonar_box['branch_id']);
    }

    // Update challenges that bind to the box with new branch id
    if(isset($data['$set']['branch_id']) && $data['$set']['branch_id']) {
      $this->update_challenges_sonar_data($data['$set']['branch_id']);
    }

    return $result;
  }

  function remove($criteria) {
    $result = $this->CI->sonar_box_model->delete($criteria);
    // echo '<hr>';
    // echo "remove ".count($result)." sonar_boxes<br>";

    if($result && count($result) > 0){
      foreach ($result as $sonar_box) {
        if(isset($sonar_box['branch_id'])) {
          $this->update_challenges_sonar_data($sonar_box['branch_id']);
        }
      }
    }

    return $result;
  }

  function update_challenges_sonar_data($branch_id){
    $this->CI->load->library('challenge_lib');

    // echo "update sonar_box: " . $sonar_box_id . '<br>';

    $criteria = array(
      '$or' => array(
        array('all_branch' => TRUE),
        array('branches' => $branch_id)
      )
    );

    $challenges = $this->CI->challenge_lib->get($criteria);

    // echo 'got '.count($challenges).' challenge to update<br>';

    if($challenges && count($challenges) > 0){
      foreach ($challenges as $challenge) {
        $this->CI->challenge_lib->generate_sonar_data('' . $challenge['_id']);
      }
    }
  }

  function get_sonar_box_title_like($title = NULL) {
    if(!strlen($title)) { return array(); }

    $criteria = array('title' => array('$regex' => '\b'.$title, '$options' => 'i'));

    return $this->CI->sonar_box_model->get($criteria);
  }
}