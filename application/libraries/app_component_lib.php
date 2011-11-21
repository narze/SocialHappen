<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * App componentLibrary
 *
 *
 * @author Metwara Narksook
 */

class App_component_lib
{
  
  private $CI;
  private $INVITE_ACCEPT_ACTION = 113;
  /**
   *  ------------------------------------------------------------------------
   *  CONSTRUCTOR
   *  ------------------------------------------------------------------------
   *
   *  Automatically check if the Mongo PECL extension has been 
   *  installed/enabled.
   * 
   * @author Metwara Narksook
   */
  
  function __construct(){
    if(!class_exists('Mongo')){
      show_error("The MongoDB PECL extension has not been installed or enabled",
       500);
    }
    $this->CI =& get_instance();
  }
  
  
  function create_index(){
    $this->CI->load->model('app_component_model','app_component');
    $this->CI->app_component->create_index();
    $this->CI->load->model('app_component_page_model','app_component_page');
    $this->CI->app_component_page->create_index();
  }
  
  /**
   * Add app_component
   * @param $app_component = array(
   *    'campaign_id' => [campaign_id] 
   *    [component_1] => [component 1 array data] 
   *    [component_2] => [component 2 array data]
   *    and so on...
   * )
   * @param $info = array(
   *  'app_id' =>
   *  'app_install_id' =>
   *  'page_id' =>
   *   // required for achievement
   * )
   * @author Metwara Narksook
   */
  function add_campaign($app_component = array()){
    $this->CI->load->model('app_component_model','app_component');
    $check_args = (isset($app_component['campaign_id']));
    
    if(!$check_args){
      return FALSE;
    }
    
    $add_campaign_result = $this->CI->app_component->add($app_component);
    return $add_campaign_result;
  }
  
  function get_campaign($campaign_id = NULL){
    return $this->CI->app_component->get_by_campaign_id($campaign_id);
  }
  
  /**
   * Add app_component_page
   * @param $app_component = array(
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
   * @author Metwara Narksook
   */
  function add_page($app_component_page = array()){
    $this->CI->load->model('app_component_page_model','app_component_page');
    
    if(!isset($app_component_page['page_id'])){
      return FALSE;
    }
    
    $add_page_result = $this->CI->app_component_page->add($app_component_page);
    $app_id = $info['app_id'] = 0;
    $app_install_id = $info['app_install_id'] = 0;
    $page_id = $app_component_page['page_id'] = (int)$app_component_page['page_id'];
    
    $add_all_achievement = TRUE;
    $added_achievement_id_list = array();
    if($add_page_result){
      $this->CI->load->library('achievement_lib');
      
      $classes = $app_component_page['classes'];
      
      for($i = 0; $i < count($classes); $i++){
        $class = $classes[$i];
        
        $class_data = array(
          'group' => 'pade_id',
          'level' => $i
        );
        
        $info = array('name' => $class['name'],
                      'description' => $class['name'],
                      'criteria_string' => array('at least '
                       . $class['invite_accepted'] . ' invite accepted'),
                      'page_id' => $page_id,
                      'class' => $class_data);
        $criteria = array('page.action.' . $this->INVITE_ACCEPT_ACTION . '.count' => $class['invite_accepted']);
        
        $added_achievement_id = $this->CI->achievement_lib->
          add_achievement_info($app_id, $app_install_id, $info, $criteria);

        if(!isset($added_achievement_id)){
          $add_all_achievement = FALSE;
          break;
        }else{
          $app_component_page['classes'][$i]['achievement_id'] = 
            '' . $added_achievement_id;
          $added_achievement_id_list[] = '' . $added_achievement_id;
        }
      }
      
      $update_page = $this->CI->app_component_page->
        update_classes_by_page_id($page_id, $app_component_page['classes']);
    }
    $result = $add_page_result && $add_all_achievement && $update_page;
    
    if(!$result){ // rollback
      
      foreach ($added_achievement_id_list as $achievement_id) {
        $this->CI->achievement_lib->delete_achievement_info($achievement_id);
      }
      
      $this->CI->app_component_page->delete($page_id);
      
      return FALSE;
    }else{
      return TRUE;
    }
  }
  
  function get_page($page_id = NULL){
    $page_id = (int)$page_id;
    return $this->CI->app_component_page->get_by_page_id($page_id);
  }
  /**
   * Add app_component_page
   * @param $page_id
   * @param $classes = array(
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
   * )
   * @author Metwara Narksook
   */
  function update_page_classes($page_id = NULL, $classes = array(),
   $info = array()){
     
  }
}
/* End of file app_component_lib.php */
/* Location: ./application/libraries/app_component_lib.php */
    