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
   *   // need for achievement
   * )
   * @author Metwara Narksook
   */
  function add_campaign($app_component = array(), $info = array()){
    $add_campaign_result = $this->CI->app_component->add($app_component);
    
    $add_achievement = FALSE;
    if($add_campaign_result){
      $this->CI->load->library('achievement_lib');
      $add_achievement = TRUE;
    }
    return $add_campaign_result && $add_achievement;
    
  }
  
  function get_campaign($campaign_id = NULL){
    return NULL;
  }
  
}
/* End of file app_component_lib.php */
/* Location: ./application/libraries/app_component_lib.php */
    