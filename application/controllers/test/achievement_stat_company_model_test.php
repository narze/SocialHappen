<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Achievement_stat_company_model_test extends CI_Controller {

  var $achievement_stat;

  function __construct(){
    parent::__construct();
    $this->load->library('unit_test');
    $this->load->model('achievement_stat_company_model','achievement_stat_company');
    $this->unit->reset_mongodb();
  }

  function __destruct(){
    $this->unit->report_with_counter();
  }

  function index(){
    $class_methods = get_class_methods($this);
    foreach ($class_methods as $method) {
        if(preg_match("/(_test)$/",$method)){
          $this->$method();
        }
    }
  }

  function create_index_test(){
    $this->achievement_stat_company->create_index();
  }

  function increment_invalid_test(){
    $company_id = 1;
    $user_id = 2;
    $info = array('page_id' => 1);
    $amount = 1;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);

    $company_id = 1;
    $user_id = NULL;
    $info = array('page_id' => 1);

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_false', 'increment', $result);


    $company_id = 1;
    $user_id = 2;
    $info = array('page_id' => NULL);
    $amount = 1;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);


    $company_id = NULL;
    $user_id = 2;
    $info = array('page_id' => 1);
    $amount = 1;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_false', 'increment', $result);

    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 0, 'increment', $total);
  }

  function increment_test(){

    $company_id = 1;
    $user_id = 2;
    $info = array('page_id' => 1,
                  'action_id' => 2,
                  'app_install_id' => 4,
                  'app_id' => 5);
    $amount = 1;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 1, 'increment', $total);

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 1, 'increment', $total);

    $company_id = 1;
    $user_id = 2;
    $info = array('page_id' => 1,
                  'action_id' => 3,
                  'app_install_id' => 4);
    $amount = 1;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 1, 'increment', $total);

    $company_id = 1;
    $user_id = 2;
    $info = array('page_id' => 1,
                  'action_id' => 3,
                  'app_install_id' => 5);
    $amount = 1;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 1, 'increment', $total);

    $company_id = 1;
    $user_id = 2;
    $info = array('page_id' => 1,
                  'action_id' => 3,
                  'app_install_id' => 6);
    $amount = 1;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 1, 'increment', $total);

    $company_id = 1;
    $user_id = 2;
    $info = array('page_id' => 1,
                  'action_id' => 4,
                  'app_install_id' => 5);
    $amount = 1;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 1, 'increment', $total);

    $company_id = 2;
    $user_id = 2;
    $info = array('page_id' => 2,
                  'action_id' => 6,
                  'app_install_id' => 7,
                  'app_id' => 9);
    $amount = 1;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 2, 'increment', $result);

    $company_id = 2;
    $user_id = 2;
    $info = array('page_id' => 2,
                  'action_id' => 9,
                  'app_install_id' => 7,
                  'app_id' => 9,
                  'campaign_id' => 10);
    $amount = 10;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 2, 'increment', $result);

    $company_id = 2;
    $user_id = 2;
    $info = array('page_id' => 3,
                  'action_id' => 9,
                  'app_install_id' => 7,
                  'app_id' => 9,
                  'campaign_id' => 10);
    $amount = 10;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 2, 'increment', $result);

  }

  function set_invalid_test(){
    $company_id = 2;
    $user_id = 2;
    $info = array('page_id' => 2,
                  'action' => 100);

    $result = $this->achievement_stat_company->set($company_id, $user_id, $info);
    $this->unit->run($result, 'is_false', 'set', $result);

    $company_id = NULL;
    $user_id = NULL;
    $info = array('page_id' => NULL,
                  'action' => 100);

    $result = $this->achievement_stat_company->set($company_id, $user_id, $info);
    $this->unit->run($result, 'is_false', 'set', $result);

    $company_id = 2;
    $user_id = 2;
    $info = array('page_id' => 2,
                  'user_id' => 100);

    $result = $this->achievement_stat_company->set($company_id, $user_id, $info);
    $this->unit->run($result, 'is_false', 'set', $result);

    $company_id = 2;
    $user_id = 2;
    $info = array('page_id' => 2,
                  'user_id' => 100,
                  'app_id' => 200);

    $result = $this->achievement_stat_company->set($company_id, $user_id, $info);
    $this->unit->run($result, 'is_false', 'set', $result);
  }

  function set_test(){
    $company_id = 2;
    $user_id = 2;
    $info = array('page_id' => 2,
                  'friend' => 100);

    $result = $this->achievement_stat_company->set($company_id, $user_id, $info);
    $this->unit->run($result, 'is_true', 'set', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 2, 'increment', $result);

    $company_id = 10;
    $user_id = 2;
    $info = array('page_id' => 10,
                  'friend' => 100);

    $result = $this->achievement_stat_company->set($company_id, $user_id, $info);
    $this->unit->run($result, 'is_true', 'set', $result);
    $total = count($this->achievement_stat_company->list_stat());
    $this->unit->run($total, 3, 'increment', $result);
  }


  function get_test(){

    $company_id = 10;
    $user_id = 2;

    $result = $this->achievement_stat_company->get($company_id, $user_id);
    $this->unit->run($result['page_id'], 10, 'get', $result);
    $this->unit->run($result['friend'], 100, 'get', $result);
    $this->unit->run($result['user_id'], 2, 'get', $result);

    $company_id = 10;
    $user_id = 200;
    $result = $this->achievement_stat_company->get($company_id, $user_id);
    $this->unit->run($result, 'is_null', 'get', $result);

    $company_id = NULL;
    $user_id = NULL;
    $result = $this->achievement_stat_company->get($company_id, $user_id);
    $this->unit->run($result, 'is_false', 'get', $result);

    $company_id = 2;
    $user_id = 2;

    $result = $this->achievement_stat_company->get($company_id, $user_id);
    $this->unit->run($result['page_id'], 2, 'get', $result);
    $this->unit->run($result['friend'], 100, 'get', $result);
    $this->unit->run($result['user_id'], 2, 'get', $result);
  }

  function score_increment_test(){
    $company_id = 1;
    $user_id = 2;
    $campaign_id = 10;
    $info = array('page_id' => 1,
                  'campaign_score' => 10,
                  'page_score' => 10,
                  'campaign_id' => $campaign_id);
    $amount = 10;

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $result = $this->achievement_stat_company->get($company_id, $user_id);
    $this->unit->run($result['campaign'][$campaign_id]['score'], 10, 'get', $result);
    $this->unit->run($result['page'][$info['page_id']]['score'], 10, 'get', $result);
    $this->unit->run($result['company_score'], 10, 'get', $result);

    $amount = NULL;
    $info = array('page_id' => 2, 'page_score' => -5);

    $result = $this->achievement_stat_company->increment($company_id, $user_id, $info, $amount);
    $this->unit->run($result, 'is_true', 'increment', $result);
    $result = $this->achievement_stat_company->get($company_id, $user_id);
    $this->unit->run($result['campaign'][$campaign_id]['score'], 10, 'get', $result);
    $this->unit->run($result['page'][$info['page_id']]['score'], -5, 'get', $result);
    $this->unit->run($result['company_score'], 5, 'get', $result);
  }
}
/* End of file achievement_stat_company_model_test.php */
/* Location: ./application/controllers/test/achievement_stat_company_model_test.php */