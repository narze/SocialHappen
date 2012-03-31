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
    $this->load->model('achievement_stat_company_model','achievement_stat_company');

  }

  function index(){
    $this->load->library('form_validation');
    $this->form_validation->set_rules('user_id', 'User_id', 'required|is_numeric|max_length[10]');      
    $this->form_validation->set_rules('company_id', 'company_id', 'required|is_numeric|max_length[10]');      
    $this->form_validation->set_rules('score', 'Score', 'required|is_numeric|max_length[10]');
      
    $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
    $user_id = $this->socialhappen->get_user_id();
    $this->load->model('company_model');
    $companys = $this->company_model->get_all();
    $company_scores = array();
    foreach($companys as $company){
      $stat_company = $this->achievement_stat_company->get($company['company_id'], $user_id);
      $company_score = $stat_company['company_score'];
      $company_scores[] = array(
        'company_id' => $company['company_id'],
        'score' => issetor($company_score, 0)
      );
    }
    $success = $this->input->get('success');
    $vars = compact('user_id', 'company_scores','success');
    $this->load->vars($vars);
    if ($this->form_validation->run() == FALSE) 
    {
      $this->load->view('dev/score');
    }
    else 
    {
      $user_id = set_value('user_id');
      $company_id = set_value('company_id');
      $score = set_value('score');
      $info = array('campaign_score' => $score,
                    'page_score' => $score,
                    'page_id' => 5,
                    'campaign_id' => 65535);
      
      $increment_company_score_result = $this->achievement_stat_company
          ->increment($company_id, $user_id, $info);
      
    
      if ($increment_company_score_result) 
      {
        redirect('dev/score?success=1', 'refresh');
      }
      else
      {
      echo 'An error occurred saving your information. Please try again later';
      
      }
    }
   
  }
}