<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tab extends CI_Controller {
	function __construct(){
		parent::construct();
	}
	
	function index(){
		$data = array(
			'header' => $this->load->view('facebook/header', 
				array(
					'title' => $company['company_name'],
					'vars' => array('company_id'=>$company_id),
					'script' => array(
						'facebook/bar'
					),
					'style' => array(
						'facebook/main'
					)
				),
			TRUE),
			'bar' => $this->load->view('facebook/bar',array(),
			TRUE),
			'main' => $this->load->view('facebook/main',array(),
			TRUE),
			'footer' => $this->load->view('facebook/footer',array(),
			TRUE)
		);
		$this->parser->parse('facebook/facebook_view', $data);
	}
	
	function bar(){
		$data = array();
	//if admin
	
	//if not admin
		$this->load->view('facebook/bar', $data);
	}
}
/* End of file tab.php */
/* Location: ./application/controllers/tab.php */
