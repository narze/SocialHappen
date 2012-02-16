<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Score extends CI_Controller {

  function __construct(){
    parent::__construct();
    if (defined('ENVIRONMENT'))
    {
      if (!(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing' ))
      {
        redirect();
      }
    }
    $this->socialhappen->check_logged_in();
    $this->load->model('achievement_stat_page_model','achievement_stat_page');

  }

  function index(){
    $this->load->library('form_validation');
    $this->form_validation->set_rules('user_id', 'User_id', 'required|is_numeric|max_length[10]');      
    $this->form_validation->set_rules('page_id', 'Page_id', 'required|is_numeric|max_length[10]');      
    $this->form_validation->set_rules('score', 'Score', 'required|is_numeric|max_length[10]');
      
    $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
    $user_id = $this->socialhappen->get_user_id();
    $this->load->model('page_model');
    $pages = $this->page_model->get_all();
    $page_scores = array();
    foreach($pages as $page){
      $stat_page = $this->achievement_stat_page->get($page['page_id'], $user_id);
      $page_score = $stat_page['page_score'];
      $page_scores[] = array(
        'page_id' => $page['page_id'],
        'score' => issetor($page_score, 0)
      );
    }
    $success = $this->input->get('success');
    $vars = compact('user_id', 'page_scores','success');
    $this->load->vars($vars);
    if ($this->form_validation->run() == FALSE) 
    {
      $this->load->view('dev/score');
    }
    else 
    {
      $user_id = set_value('user_id');
      $page_id = set_value('page_id');
      $score = set_value('score');
      $info = array('campaign_score' => $score,
                    'page_score' => $score,
                    'campaign_id' => 65535);
      var_dump_pre(compact('user_id','page_id','score','info'));
      $increment_page_score_result = $this->achievement_stat_page
          ->increment($page_id, $user_id, $info);
      
    
      if ($increment_page_score_result) 
      {
        redirect('dev/score?success=1');
      }
      else
      {
      echo 'An error occurred saving your information. Please try again later';
      
      }
    }
   
  }
}