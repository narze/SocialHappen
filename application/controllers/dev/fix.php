<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Fix
 * @category Controller
 */
class Fix extends CI_Controller {

  function __construct(){
    parent::__construct();
    if (defined('ENVIRONMENT'))
    {
      if (!(ENVIRONMENT == 'development' || ENVIRONMENT == 'testing'))
      {
        if($this->input->get('happy') !== 'everyday'){
          redirect();
        }
      }
    }
  }

  function index(){
    
  }

  function build_sonar_box_data() {
    $this->load->model('sonar_box_model');
    $this->load->model('challenge_model');

    $all_challenges = $this->challenge_model->get(array(), 0);

    foreach($all_challenges as $challenge) {
      if(!$challenge['sonar_frequency']) {
        continue;
      }

      $challenge_id = get_mongo_id($challenge);
      $query = array('challenge_id' => $challenge_id);

      //if sonar data isn't present, create them
      if(!$this->sonar_box_model->get($query)) {
        $this->sonar_box_model->add($query, array(
          'name' => $challenge['detail']['name'],
          'info' => array(),
          'challenge_id' => $challenge_id,
          'data' => $challenge['sonar_frequency']
        ));
      }
    }

    echo 'Build completed';
  }
}
/* End of file fix.php */
/* Location: ./application/controllers/dev/fix.php */