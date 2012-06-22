<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Play extends CI_Controller {

  function __construct(){
    parent::__construct();
  }

  /**
   * Play page
   */
  function index(){
    $this->load->library('apiv2_lib');
    $app_data = $this->input->get('app_data', TRUE);

    $facebook_data = array(
      'facebook_app_id' => $this->config->item('facebook_app_id'),
      'facebook_app_scope' => $this->config->item('facebook_player_scope'),
      'facebook_channel_url' => $this->facebook->channel_url
    );

    $this->load->vars(array(
      'static_fb_root' => $this->load->view('player/static_fb_root', $facebook_data, TRUE)
    ));

    if(!$app_data){
      $app_data_array = array(
        'app_id' => 0,
        'app_secret_key' => 0,
      );
      $app_data = base64_encode(json_encode($app_data_array));
      $data['true_app_data'] = false;

    } else {
      $data['true_app_data'] = true;
      $app_data_array = json_decode(base64_decode($app_data), TRUE);
    }

    $data['app_data'] = $app_data;
    $data['app_data_array'] = $app_data_array;

    $template = array(
      'title' => 'Welcome to SocialHappen',
      'styles' => array(
        'common/bootstrap',
        'common/bootstrap-responsive',
        'common/bar',
        'play/play'
      ),
      'body_views' => array(
        'common/fb_root' => array(
          'facebook_app_id' => $this->config->item('facebook_app_id'),
          'facebook_channel_url' => $this->facebook->channel_url,
          'facebook_app_scope' => $this->config->item('facebook_player_scope')
        ),
        // '../../assets/passport/templates/header/navigation.html' => NULL,
        'bar/plain_bar_view' => array(),
        'play/play_view' => $data,
        'common/vars' => array(
          'vars' => array(
            'base_url' => base_url()
          )
        )
      ),
      'scripts' => array(
        'https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
        'common/jquery.masonry.min',
        'common/jquery.timeago',
        'common/underscore-min',
        'common/bootstrap.min',
        'common/plain-bar',
        'play/play'
      )
    );
    $this->load->view('common/template', $template);
  }
}

/* End of file play.php */
/* Location: ./application/controllers/play.php */