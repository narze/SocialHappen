<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model for homepage : homepage, invite and sharebutton
 * ex. array(
 *		'app_install_id' => [app_install_id],
 *		'enable' => [boolean, FALSE if not set],
 *		'image' => [image url],
 *		'message' => [text]
 * 	)
 * @author Manassarn M.
 */
class Homepage_model extends CI_Model {

	/**
	 * Connect to mongodb
	 * @author Manassarn M.
	 */
	function __construct(){
		parent::__construct();
		$this->load->helper('mongodb');
		$this->collection = sh_mongodb_load( array(
			'collection' => 'app_component_homepage'
		));
	}

	/**
	 * Drop homepage collection
	 * @author Manassarn M.
	 */
	function drop_collection(){
		return $this->collection->drop();
	}

	/**
	 * Create index for homepage collection
	 * @author Manassarn M.
	 */
	function create_index(){
		return $this->collection->deleteIndexes()
			&& $this->collection->ensureIndex(array('app_install_id'=>1), array('unique' => 1));
	}

	/**
	 * Count all homepage
	 * @author Manassarn M.
	 */
	function count_all(){
		return $this->collection->count();
	}

	//Homepage

	/**
	 * Check homepage data
	 * @param $homepage
	 * @author Manassarn M.
	 */
	function homepage_data_check($homepage = array()){
		return arenotempty($homepage, array('app_install_id','image', 'message'));
	}

	/**
	 * Process homepage data
	 * @param $homepage
	 * @author Manassarn M.
	 */
	function homepage_data_process($homepage = array()){
		if(!isset($homepage['enable'])){
			$homepage['enable'] = FALSE;
		}
		return $homepage;
	}

	/**
	 * Get homepage by app_install_id
	 * @param $app_install_id
	 * @author Manassarn M.
	 */
	function get_homepage_by_app_install_id($app_install_id = NULL){
		$result = $this->collection
			->findOne(array('app_install_id' => (int) $app_install_id));
		return $result;
	}

	/**
	 * Update homepage by app_install_id
	 * @param $app_install_id
	 * @param $homepage = array(
	 *		'enable' => [boolean, FALSE if not set],
	 *		'image' => [image url],
	 *		'message' => [text]'image' => [message image url]
	 *		)
	 * @author Manassarn M.
	 */
	function update_homepage_by_app_install_id($app_install_id = NULL, $homepage = NULL){
		$check_args = !empty($app_install_id) && $this->homepage_data_check($homepage);
		if(!$check_args){
			return FALSE;
		} else {
			$homepage['app_install_id'] = $app_install_id = (int) $app_install_id;
			$homepage = $this->homepage_data_process($homepage);
			return $this->update(array('app_install_id' => $app_install_id), $homepage);
		}
	}

	/**
	 * Add an homepage
	 * @param $homepage = array(
	 *		'app_install_id' => [app_install_id],
	 *		'enable' => [boolean, FALSE if not set],
	 *		'image' => [image url],
	 *		'message' => [text]
	 * 		)
	 * @author Manassarn M.
	 */
	function add($homepage = array()){
		$check_args = $this->homepage_data_check($homepage);
		if(!$check_args){
			return FALSE;
		} else {
			$homepage['app_install_id'] = (int) $homepage['app_install_id'];
			return $this->collection->insert($homepage);
		}
	}

  /**
   * delete homepage
   * @param app_install_id
   *
   * @return result bolean
   *
   * @author Metwara Narksook
   */
  function delete($app_install_id = NULL){
    $check_args = isset($app_install_id);
    if($check_args){
      return $this->collection
                  ->remove(array("app_install_id" => $app_install_id),
                  array('$atomic' => TRUE));
    } else {
      return FALSE;
    }
  }

  function update($query, $data) {
    try {
      $update_result = $this->collection->update($query, $data, array('safe' => TRUE));
      return isset($update_result['n']) && ($update_result['n'] > 0);
    } catch(MongoCursorException $e){
      log_message('error', 'Mongodb error : '. $e);
      return FALSE;
    }
  }
}

/* End of file homepage_model.php */
/* Location: ./application/models/homepage_model.php */