<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Challenge extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
	}

	function index($company_id = NULL) {
		$template = array(
      'title' => 'Challenge Settings',
      'styles' => array(
        'common/bootstrap.min',
        'common/bootstrap-responsive.min',
        'common/bar'
      ),
      'body_views' => array(
        'common/fb_root' => array(
          'facebook_app_id' => $this->config->item('facebook_app_id'),
          'facebook_channel_url' => $this->facebook->channel_url,
          'facebook_app_scope' => $this->config->item('facebook_player_scope')
        ),
        'bar/plain_bar_view' => array(),
        'settings/challenge_view' => array(),
        'common/vars' => array(
          'vars' => array(
            // 'base_url' => base_url()
          )
        )
      ),
      'requirejs' => 'js/settings-challenge'
    );
    $this->load->view('common/template', $template);
	}

	function add($company_id = NULL) {

	}

	function update($company_id = NULL, $challenge_id = NULL) {

	}

	function remove($company_id = NULL, $challenge_id = NULL) {

	}
}
/* End of file challenge.php */
/* Location: ./application/controllers/settings/challenge.php */