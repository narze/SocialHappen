<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Challenge_model extends CI_Model {

	function __construct() {
		parent::__construct();
		$this->load->helper('mongodb');
		$this->collection = $this->challenge = sh_mongodb_load( array(
			'collection' => 'challenge'
		));
		$this->int_values = array('company_id');
	}

	//Basic functions (reindex & CRUD)
	function recreateIndex() {
		return $this->collection->deleteIndexes()
			&& $this->collection->ensureIndex(array('company_id' => 1))
			&& $this->collection->ensureIndex(array('location' => '2d'), array('bits' => 26))
			&& $this->collection->ensureIndex(array('locations' => '2d'), array('bits' => 26));
	}

	function add($data)
	{
		$data = array_cast_int($data, $this->int_values);
		try	{
			$this->collection->insert($data, array('safe' => TRUE));
			return ''.$data['_id'];
		} catch(MongoCursorException $e){
			log_message('error', 'Mongodb error : '. $e);
			return FALSE;
		}
	}

	function get($query, $limit = 100, $offset = 0, $sort = NULL){
		$query = array_cast_int($query, $this->int_values);
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

	function get_sort($query, $sort = FALSE, $limit = 100){
		$query = array_cast_int($query, $this->int_values);
		if($sort) {
			$result = $this->collection->find($query)->sort($sort)->limit($limit);
		} else {
			$result = $this->collection->find($query)->limit($limit);
		}
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
	//End of basic functions

	/**
	 * Get all companies that have challenge
	 */
	function get_distinct_company() {
		$result = $this->mongo_db->command(array("distinct" => "challenge", "key" => "company_id"));
		return $result['ok'] ? $result['values'] : array();
	}

	function count($criteria = array()) {
		return $this->collection->count($criteria);
	}
}