<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Branch_model extends CI_Model {

  var $attributes = array('location', 'company_id', 'title', 'address', 'telephone', 'photo', 'company_id');

  function __construct() {
    parent::__construct();
    $this->load->helper('mongodb');
    $this->collection = $this->branch = sh_mongodb_load( array(
      'collection' => 'branch'
    ));
    $this->int_values = array('company_id');
  }

  //Basic functions (reindex & CRUD)
  function recreateIndex() {
    return $this->collection->deleteIndexes()
      && $this->collection->ensureIndex(array('company_id' => 1))
      && $this->collection->ensureIndex(array('location' => '2d'), array('bits' => 26));
  }

  function add($data)
  {
    $data = array_cast_int($data, $this->int_values);
    try {
      $this->collection->insert($data, array('safe' => TRUE));
      if($data){
        $data['_id'] = '' . $data['_id'];
      }
      return $data;
    } catch(MongoCursorException $e){
      log_message('error', 'Mongodb error : '. $e);
      return FALSE;
    }
  }

  function get($query, $limit = 100){
    $query = array_cast_int($query, $this->int_values);
    $result = $this->collection->find($query)->sort(array('_id' => -1))->limit($limit);
    return cursor2array($result);
  }

  function getOne($query){
    $query = array_cast_int($query, $this->int_values);
    $result = $this->collection->findOne($query);
    return obj2array($result);
  }

  function update($query, $data) {
    $query = array_cast_int($query, $this->int_values);
    try {
      $update_result = $this->collection->update($query, $data, array('safe' => TRUE));
      return isset($update_result['n']) && ($update_result['n'] > 0);
    } catch(MongoCursorException $e){
      log_message('error', 'Mongodb error : '. $e);
      return FALSE;
    }
  }

  function delete($query){
    $query = array_cast_int($query, $this->int_values);
    return $this->collection->remove($query, array('$atomic' => TRUE));
  }
}