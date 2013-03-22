<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sonar_box_model extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->collection = sh_mongodb_load( array(
			'collection' => 'sonar_box'
		));
		$this->int_values = array();
	}

	//Basic functions (reindex & CRUD)
	function recreateIndex() {
		return $this->collection->deleteIndexes()
			&& $this->collection->ensureIndex(array('data' => 1))
			&& $this->collection->ensureIndex(array('name' => 1))
			&& $this->collection->ensureIndex(array('id' => 1), array('unique' => 1))
			&& $this->collection->ensureIndex(array('challenge_id' => 1))
			&& $this->collection->ensureIndex(array('action_data_id' => 1));
	}

	function add($data)	{
		$data = array_cast_int($data, $this->int_values);
		try	{
			$this->collection->insert($data, array('safe' => TRUE));
			return ''.$data['_id'];
		} catch(MongoCursorException $e){
			log_message('error', 'Mongodb error : '. $e);
			return FALSE;
		}
	}

	function get($query, $sort = array()){
		$query = array_cast_int($query, $this->int_values);
		$result = $this->collection->find($query);
		if($sort) {
			$result = $result->sort($sort);
		}
		return cursor2array($result);
	}

	function get_all($query, $limit = 100, $offset = 0, $sort = NULL){
		// $query = array_cast_int($query, $this->int_values);
		$result = $this->collection->find($query);

		if(!$sort || !is_array($sort)) {
			$sort = array('_id' => -1);
		}

		$result = $result->sort($sort);
		$result = $result->skip($offset);

		if($limit) {
			$result = $result->limit($limit);
		}

		return cursor2array($result);
	}

	function getOne($query){
		$query = array_cast_int($query, $this->int_values);
		$result = $this->collection->findOne($query);
		return obj2array($result);
	}

	function count($criteria = array()) {
		return $this->collection->count($criteria);
	}

	function update($query, $data, $options = array()) {
		$query = array_cast_int($query, $this->int_values);
	  try {
	    $update_result = $this->collection->update($query, $data, array_merge(array('safe' => TRUE), $options));
	    return isset($update_result['n']) && ($update_result['n'] > 0);
	  } catch(MongoCursorException $e){
	    log_message('error', 'Mongodb error : '. $e);
	    return FALSE;
	  }
	}

  function upsert($query, $data) {
  	$query = array_cast_int($query, $this->int_values);
    try {
      $update_result = $this->collection->update($query, $data, array('safe' => TRUE, 'upsert' => TRUE));
      return isset($update_result['n']) && ($update_result['n'] > 0);
    } catch(MongoCursorException $e){
      log_message('error', 'Mongodb error : '. $e);
      return FALSE;
    }
  }

	function delete($query){
		$query = array_cast_int($query, $this->int_values);
		$result = $this->get_all($query, 100000);
		$this->collection->remove($query, array('$atomic' => TRUE, '$safe' => TRUE));
		return $result;
	}
	//End of basic functions

	/**
	 * Add user's token
	 * @param array $data
	 * @return $data with login_token
	 */
	function add_sonar_box($data) {
		if(!all_not_null($data, array('id', 'name'))) {
			return FALSE;
		}

		if($this->add($data)) {
			return $data;
		}

		return FALSE;
	}

	/**
	 * Generate sonar data (8 digit of base-4)
	 */
	function _generate_sonar_data() {
		$number = mt_rand(0, 65535);
		return sprintf('%08d', base_convert($number, 10, 4));
	}

	function generate_safe_sonar_data() {
		while($sonar = $this->_generate_sonar_data()) {
			if(!$this->check_sonar_data($sonar)) {
				return $sonar;
			}
		}
		return FALSE;
	}

	function check_sonar_data($data) {
		$sonar_boxes = $this->get(array('data' => $data));
		return !!$sonar_boxes;
	}
}