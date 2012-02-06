<?php
/**
 * achievement stat page model class for achievement stat page object
 * @author Metwara Narksook
 */
class Achievement_stat_page_model extends CI_Model {

  var $page_id = '';
  var $user_id = '';
  
  /**
   * constructor
   * 
   * @author Metwara Narksook
   */
  function __construct() {
    parent::__construct();
    $this->load->helper('mongodb');
    $this->achievement_stat_page = sh_mongodb_load( array(
      'database' => 'achievement',
      'collection' => 'achievement_stat_page'
    ));
  }
    
  /**
   * create index for collection
   * 
   * @author Metwara Narksook
   */
  function create_index(){
    return $this->achievement_stat_page->ensureIndex(array('page_id' => 1,
                    'user_id' => 1));
  }
     
  /**
   * increment stat of achievement
   * if increment non-exist stat, it'll create new stat entry
   * 
   * @param page_id int
   * @param user_id int user_id
   * @param info array of data to add 
   *        may contain['action_id', 'app_install_id' ,'app_id', 'campaign_id']
   *        ex. array('action_id'=>5,'app_install_id'=>2)
   * @param amount int amount to increment
   * 
   * @return result boolean
   * 
   * @author Metwara Narksook
   */
  function increment($page_id = NULL, $user_id = NULL,
     $info = array(), $amount = 1){
    
    $check_args = (isset($page_id) && isset($user_id));
    
    if($check_args){
      $criteria = array('page_id' => (int)$page_id, 'user_id' => (int)$user_id);
        
      $inc = array();
      $result = TRUE;
      if(isset($info['action_id'])){

        $inc['action.' . $info['action_id'] . '.count'] = $amount;

        $result = $this->achievement_stat_page->update($criteria,
          array('$inc' => $inc), TRUE);
      }else if(isset($info['campaign_score']) && isset($info['campaign_id'])){
        
        $inc['campaign.' . $info['campaign_id'] . '.score'] = (int) $info['campaign_score'];
        
        if(isset($info['page_score'])){
          $inc['page_score'] = (int) $info['page_score'];
        }
        
        $result = $this->achievement_stat_page->update($criteria,
          array('$inc' => $inc), TRUE);
      }else if(isset($info['page_score'])){
        $inc['page_score'] = (int) $info['page_score'];
        $result = $this->achievement_stat_page->update($criteria,
          array('$inc' => $inc), TRUE);
      }
      
      return $result;
    }else{
      return FALSE;
    }
  }
  
  /**
   * set achievement stat
   * @param page_id int
   * @param user_id int user_id
   * @param info array of data to set
   * 
   * @return result boolean
   */
  function set($page_id = NULL, $user_id = NULL, $info = array()){
    $check_args = isset($page_id) && isset($user_id)
                  && empty($info['action']) && !isset($info['page_id'])
                   && empty($info['user_id']);
    
    if($check_args){
      $criteria = array('page_id' => (int)$page_id, 'user_id' => (int)$user_id);
      
      $result = $this->achievement_stat_page->update($criteria,
        array('$set' => $info), TRUE);
        
      return $result;
    }else{
      return FALSE;
    }
  }
  
  
  /**
   * get stat achievement
   * 
   * @param page_id
   * @param user_id
   * 
   * @return result
   * 
   * @author Metwara Narksook
   */
  function get($page_id = NULL, $user_id = NULL){
    $check_args = isset($page_id) && isset($user_id);
    if($check_args){
      
      
      $res = $this->achievement_stat_page->find(array('page_id' => (int)$page_id,
                                                 'user_id' => (int)$user_id))
                                    ->limit(1);
      $result = array();
      foreach ($res as $stat) {
        $result[] = $stat;
      }
      return count($result) > 0 ? $result[0] : NULL;
    }else{
      return FALSE;
    }
  }
  
  /**
   * list achievement stat
   * 
   * @param criteria array of criteria
   * 
   * @return result array
   * 
   */
  function list_stat($criteria = array()){
    $res = $this->achievement_stat_page->find($criteria);
    
    $result = array();
    foreach ($res as $stat) {
      $result[] = $stat;
    }
    return $result;
  }
  
  /**
   * drop entire collection
   * you will lost all achievement_stat_page data
   * 
   * @author Metwara Narksook
   */
  function drop_collection(){
    return $this->achievement_stat_page->drop();
  }
}

/* End of file achievement_stat_model.php */
/* Location: ./application/models/achievement_stat_model.php */