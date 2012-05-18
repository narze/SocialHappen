<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class QR extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	/**
	 * Show qr image
	 * @author Manassarn M.
	 */
	function index(){
		//Config
		$config['cacheable']    = FALSE; //boolean, the default is true
		$config['cachedir']     = ''; //string, the default is application/cache/
		$config['errorlog']     = ''; //string, the default is application/logs/
		$config['quality']      = TRUE; //boolean, the default is true
		$config['size']         = ''; //integer, the default is 1024

		$this->load->library('ciqrcode', NULL, $config);

		// $this->ciqrcode->initialize($config);

		//Generate
		$path = $this->input->get('path');
		$params['data'] = base_url($path);

		header("Content-Type: image/png");
		$this->ciqrcode->generate($params);
	}
}

/* End of file qr.php */
/* Location: ./application/controllers/qr.php */