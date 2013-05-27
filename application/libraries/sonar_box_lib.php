<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Sonar_box_lib {

  function __construct() {
    $this->CI =& get_instance();
    $this->CI->load->model('sonar_box_model');
  }

  function add($data) {
    if($id = $this->CI->sonar_box_model->add($data)) {
      if(isset($data['challenge_id'])) {
        $this->update_challenges_sonar_data($data['challenge_id']);
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
      $result['_id'] = get_mongo_id($result);
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

    // Update the old challenge
    if(isset($sonar_box['challenge_id']) && $sonar_box['challenge_id']) {
      $this->update_challenges_sonar_data($sonar_box['challenge_id']);
    }

    // Update the new challenge
    if(isset($data['$set']['challenge_id']) && $data['$set']['challenge_id']) {
      $this->update_challenges_sonar_data($data['$set']['challenge_id']);
    }

    return $result;
  }

  function remove($criteria) {
    $result = $this->CI->sonar_box_model->delete($criteria);
    // echo '<hr>';
    // echo "remove ".count($result)." sonar_boxes<br>";

    if($result && count($result) > 0){
      foreach ($result as $sonar_box) {
        if(isset($sonar_box['challenge_id'])) {
          $this->update_challenges_sonar_data($sonar_box['challenge_id']);
        }
      }
    }

    return $result;
  }

  function update_challenges_sonar_data($challenge_id){
    $this->CI->load->library('challenge_lib');

    $this->CI->challenge_lib->generate_codes($challenge_id);
  }

  function get_sonar_box_title_like($title = NULL) {
    if(!strlen($title)) { return array(); }

    $criteria = array('title' => array('$regex' => '\b'.$title, '$options' => 'i'));

    return $this->CI->sonar_box_model->get($criteria);
  }

  function update_sonar_boxes_challenge_and_action_data($sonar_box_ids = array(), $challenge_id = NULL, $action_data_id = NULL) {
    if(!$sonar_box_ids) {
      return FALSE;
    }

    foreach($sonar_box_ids as &$sonar_box_id) {
      $sonar_box_id = new MongoId($sonar_box_id);
    }

    return $this->CI->sonar_box_model->update(array('_id' => array('$in' => $sonar_box_ids)), array('$set' => array('challenge_id' => $challenge_id, 'action_data_id' => $action_data_id)), array('multiple' => 1));
  }
}