<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video extends CI_Controller {

    function __construct(){
        parent::__construct();
    }

    /**
     * Video page
     * @author Weerapat P.
     */
    function index(){
        $this->load->view('video/video_view');
    }
}