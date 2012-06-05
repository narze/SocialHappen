<?php
/**
 * achievement stat company model class for achievement stat company object
 * @author Metwara Narksook
 */
class Achievement_stat_company_model extends CI_Model {

  var $company_id = '';
  var $user_id = '';
  
  /**
   * constructor
   * 
   * @author Metwara Narksook
   */
  function __construct() {
    parent::__construct();
    $this->load->helper('mongodb');
    $this->achievement_stat_company = sh_mongodb_load( array(
      'collection' => 'achievement_stat_company'
    ));
  }
    
  /**
   * create index for collection
   * 
   * @author Metwara Narksook
   */
  function create_index(){
    return $this->achievement_stat_company->deleteIndexes() 
      && $this->achievement_stat_company->ensureIndex(array('company_id' => 1,
                    'user_id' => 1));
  }
     
  /**
   * increment stat of achievement
   * if increment non-exist stat, it'll create new stat entry
   * 
   * @param company_id int
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
  function increment($company_id = NULL, $user_id = NULL,
     $info = array(), $amount = 1){
    
    $check_args = (isset($company_id) && isset($user_id));
    
    if($check_args){
      $criteria = array('company_id' => (int)$company_id, 'user_id' => (int)$user_id);
        
      $inc = array();
      $result = TRUE;
      $do_increment = FALSE;

      if(isset($info['action_id'])){
        $inc['action.' . $info['action_id'] . '.count'] = $amount;
        $do_increment = TRUE;
      }

      if(isset($info['campaign_score']) && isset($info['campaign_id'])){
        $inc['campaign.' . $info['campaign_id'] . '.score'] = (int) $info['campaign_score'];
        $do_increment = TRUE;
      }
      
      if(isset($info['page_score']) && isset($info['page_id'])){
        $inc['page.'.$info['page_id'].'.score'] = (int) $info['page_score'];
        $inc['company_score'] = (int) $info['page_score'];
        $do_increment = TRUE;
      }

      if(isset($info['company_score'])) {
        $inc['company_score'] = (int) $info['company_score'];
        $do_increment = TRUE;
      }


      if($do_increment) {
        $result = $this->achievement_stat_company->update($criteria,
          array('$inc' => $inc), TRUE);
      }
      return $result;
    } else {
      return FALSE;
    }
  }
  
  /**
   * set achievement stat
   * @param company_id int
   * @param user_id int user_id
   * @param info array of data to set
   * 
   * @return result boolean
   */
  function set($company_id = NULL, $user_id = NULL, $info = array()){
    $check_args = isset($company_id) && isset($user_id)
                  && empty($info['action'])
                   && empty($info['user_id']);
    
    if($check_args){
      $criteria = array('company_id' => (int)$company_id, 'user_id' => (int)$user_id);
      
      $result = $this->achievement_stat_company->update($criteria,
        array('$set' => $info), TRUE);
        
      return $result;
    } else {
      return FALSE;
    }
  }
  
  
  /**
   * get stat achievement
   * 
   * @param company_id
   * @param user_id
   * 
   * @return result
   * 
   * @author Metwara Narksook
   */
  function get($company_id = NULL, $user_id = NULL){
    $check_args = isset($company_id) && isset($user_id);
    if($check_args){
      
      
      $res = $this->achievement_stat_company->find(array('company_id' => (int)$company_id,
                                                 'user_id' => (int)$user_id))
                                    ->limit(1);
      $result = array();
      foreach ($res as $stat) {
        $result[] = $stat;
      }
      return count($result) > 0 ? $result[0] : NULL;
    } else {
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
    $res = $this->achievement_stat_company->find($criteria);
    
    $result = array();
    foreach ($res as $stat) {
      $result[] = $stat;
    }
    return $result;
  }
  
  /**
   * drop entire collection
   * you will lost all achievement_stat_company data
   * 
   * @author Metwara Narksook
   */
  function drop_collection(){
    return $this->achievement_stat_company->drop();
  }
}

/* End of file achievement_stat_model.php */
/* Location: ./application/models/achievement_stat_model.php */