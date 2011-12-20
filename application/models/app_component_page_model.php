<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Model for app_component_page : classes
 * ex. 
 *  array(
 *    'page_id' => [page_id] (unique)
 *    'classes' => array(
 *      array(
 *        "name" => "Founding",
 *        "invite_accepted" => 3,
 *        "achievement_id" => "4ec7507b6803fac21600000f" 
 *      ),
 *      array(
 *        "name" => "VIP",
 *        "invite_accepted" => 10,
 *        "achievement_id" => "4ec7507b6803fac216000010" 
 *      ),
 *      array(
 *        "name" => "Prime",
 *        "invite_accepted" => 50,
 *        "achievement_id" => "4ec7507b6803fac216000011" 
 *      )
 *    )
 *  )
 * @author Metwara Narksook
 */
class App_component_page_model extends CI_Model {
  
  /**
   * Connect to mongodb
   * @author Metwara Narksook
   */
  function __construct(){
    parent::__construct();
    $this->load->helper('mongodb');
    $this->app_component_page = sh_mongodb_load( array(
      'database' => 'campaign',
      'collection' => 'app_component_page'
    ));
  }
  
  /** 
   * Drop homepage collection
   * @author Metwara Narksook
   */
  function drop_collection(){
    return $this->app_component_page->drop();
  }
  
  /**
   * Create index for homepage collection
   * @author Metwara Narksook
   */
  function create_index(){
    return $this->app_component_page->ensureIndex(array('page_id'=>1), array('unique' => 1));
  }
  
  /**
   * Count all homepage
   * @author Metwara Narksook
   */
  function count_all(){
    return $this->app_component_page->count();
  }
  
  /**
   * Get classes by page_id
   * @param $page_id
   * @author Metwara Narksook
   */
  function get_classes_by_page_id($page_id = NULL){
    $result = $this->app_component_page
      ->findOne(array('page_id' => (int) $page_id));
    
    $result = obj2array($result);
    return issetor($result['classes'], NULL);
  }
  
  /**
   * Update classes by page_id
   * @param $page_id
   * @param $classes = array(
   *    'classes' => array(
   *      array(
   *        "name" => "Founding",
   *        "invite_accepted" => 3,
   *        "achievement_id" => "4ec7507b6803fac21600000f" 
   *      ),
   *      array(
   *        "name" => "VIP",
   *        "invite_accepted" => 10,
   *        "achievement_id" => "4ec7507b6803fac216000010" 
   *      ),
   *      array(
   *        "name" => "Prime",
   *        "invite_accepted" => 50,
   *        "achievement_id" => "4ec7507b6803fac216000011" 
   *      )
   *    )
   * @author Metwara Narksook
   */
  function update_classes_by_page_id($page_id = NULL, $classes = NULL){
    $check_args = !empty($page_id);
    if(!$check_args){
      return FALSE;
    } else {
      $page_id = (int) $page_id;      
      return $this->app_component_page->update(array('page_id' => $page_id),
        array('$set' => array(
          'classes' => $classes
          )
        )
      );
    }
  }
  
  
  //App component
  
  /**
   * Get app_component_page by page_id
   * @param $page_id
   * @author Metwara Narksook
   */
  function get_by_page_id($page_id = NULL){
    $result = $this->app_component_page
      ->findOne(array('page_id' => (int) $page_id));
    
    $result = obj2array($result);
    return $result;
  }
  
  /**
   * Add app_component_page by page_id
   * @param $app_component_page = array(
   *    'page_id' => [page_id] 
   *    'classes' => array(
   *      array(
   *        "name" => "Founding",
   *        "invite_accepted" => 3,
   *        "achievement_id" => "4ec7507b6803fac21600000f" 
   *      ),
   *      array(
   *        "name" => "VIP",
   *        "invite_accepted" => 10,
   *        "achievement_id" => "4ec7507b6803fac216000010" 
   *      ),
   *      array(
   *        "name" => "Prime",
   *        "invite_accepted" => 50,
   *        "achievement_id" => "4ec7507b6803fac216000011" 
   *      )
   *    )
   * )
   * @author Manasssarn M.
   */
  function add($app_component_page = array()){
    $check_args = !empty($app_component_page['page_id']);
    if(!$check_args){
      return FALSE;
    } else {
      $app_component_page['page_id'] = (int) $app_component_page['page_id'];
      return $this->app_component_page->insert($app_component_page);
    }
  }
  
  /**
   * delete app_component_page 
   * @param page_id
   * 
   * @return result bolean
   * 
   * @author Metwara Narksook
   */
  function delete($page_id = NULL){
    $check_args = isset($page_id);
    if($check_args){
      return $this->app_component_page
                  ->remove(array("page_id" => $page_id), 
                  array('$atomic' => TRUE));
    }else{
      return FALSE;
    }
  }
}

/* End of file app_component_page_model.php */
/* Location: ./application/models/app_component_page_model.php */