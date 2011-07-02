<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audit extends CI_Controller {
	
	var $audit_lib = '';
	/**
	 * construct method
	 */
	function __construct(){
		parent::__construct();
		$this->load->library('audit_lib');
	}
	
	/**
	 * index method
	 */
	function index(){
		echo 'index created';
		echo $this->audit_lib->create_index();
	}
	
	function today(){
		date_default_timezone_set('Asia/Bangkok');
		$start = time();
		$end = time();
		echo 'today : ' . $start . ' - ' . $end;
		echo '</br>';
		
		$time = time();
		$hours = date('G' ,$start);
		$minutes = date('i' ,$start);
		$seconds = date('s' ,$start);
		echo 'ts: ' . $hours . ' ' . $minutes . ' ' . $seconds;
		$start = time() - $hours * 3600 - $minutes * 60 - $seconds;
		echo '</br>';
		echo '1308589200 = ' . date(DATE_RFC822, 1308589200);
		echo '</br>';
		echo '1308675600 = ' . date(DATE_RFC822, 1308675600);
		echo '</br>';
		echo '1308591892 = ' . date(DATE_RFC822, 1308591892);
		echo '</br>';
		echo $start;
		echo '</br>';
		$time = time();
		$start = mktime(0, 0, 0, date('n', $time), date('j', $time), date('Y', $time));
		echo $start;
		echo '</br>';
		echo 'start: ' . $this->_get_start_day_time(1308553200);
		echo '</br>';
		echo 'end: ' . $this->_get_end_day_time(1308553200);
		echo '</br>';
		echo date(DATE_RFC822, $this->_get_start_day_time($time));
		echo '</br>';
		echo date(DATE_RFC822, $this->_get_end_day_time($time));
		echo '</br>';
		echo $this->audit_lib->convert_statdate_to_date(20100531);
		echo '</br>';
		echo date(DATE_RFC822, $this->audit_lib->convert_statdate_to_date(20100531));
	}

	function count_audit(){
		$key = 'subject';
		$app_id = 1;
		$action_id = 1;
		$criteria = array('app_install_id' => 16);
		$date = 20110620;
		echo '<pre>';
		var_dump($this->audit_lib->count_audit($key, $app_id, $action_id, $criteria, $date));
		echo '</pre>';
	}
	
	function graph(){
		echo '<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="'.base_url().'assets/js/stat/excanvas.min.js"></script><![endif]-->
			<script language="javascript" type="text/javascript" src="'.base_url().'assets/js/stat/jquery-1.5.1.min.js"></script>
			<script language="javascript" type="text/javascript" src="'.base_url().'assets/js/stat/jquery.jqplot.min.js"></script>
		     <script language="javascript" type="text/javascript" src="'.base_url().'assets/js/stat/jqplot.highlighter.min.js"></script>
		     <script language="javascript" type="text/javascript" src="'.base_url().'assets/js/stat/jqplot.cursor.min.js"></script>
		     <script language="javascript" type="text/javascript" src="'.base_url().'assets/js/stat/jqplot.dateAxisRenderer.min.js"></script>
		     <script language="javascript" type="text/javascript" src="'.base_url().'assets/js/stat/jqplot.canvasTextRenderer.min.js"></script>
		     <script language="javascript" type="text/javascript" src="'.base_url().'assets/js/stat/jqplot.canvasAxisTickRenderer.min.js"></script>
		     <script language="javascript" type="text/javascript" src="'.base_url().'assets/js/stat/jqplot.pointLabels.min.js"></script>
		     <link rel="stylesheet" type="text/css" href="'.base_url().'assets/js/stat/jquery.jqplot.min.css" />';
		$data = array(array('20080223' => 5,
					'20080323' => 10,
					'20080423' => 4,
					'20080523' => 7),
					array('20080223' => 8,
					'20080323' => 5,
					'20080423' => 9,
					'20080523' => 12),
					array('20080223' => 11,
					'20080323' => 15,
					'20080423' => 2,
					'20080523' => 22,
					'20080623' => 18));
		$data_label = array('line1', 'line2', 'line3');
		$title = 'hello world';
		$div = array('id' => 'chart1',
					'width' => 640,
					'height' => 480,
					'class' => '',
					'xlabel' => 'Dates',
					'ylabel' => 'Active Users');
		//echo json_encode($data);
		echo $this->audit_lib->render_stat_graph($data_label, $data, $title, $div);
	}

	function _get_start_day_time($timestamp){
		$start = mktime(0, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
		return $start;
	}
	
	function _get_end_day_time($timestamp){
		$end = mktime(24, 0, 0, date('n', $timestamp), date('j', $timestamp), date('Y', $timestamp));
		return $end;
	}
	
	function add_audit_action(){
		$app_id = 0;
		$action_id = 1;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$description = 'platform visit';
		$result = $this->_add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		
		$app_id = 0;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = FALSE;
		$stat_campaign = FALSE;
		$description = 'platform register';
		$result = $this->_add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		
		$app_id = 1;
		$action_id = 1;
		$stat_app = TRUE;
		$stat_page = TRUE;
		$stat_campaign = TRUE;
		$description = 'app1 visit';
		$result = $this->_add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		
		$app_id = 1;
		$action_id = 2;
		$stat_app = TRUE;
		$stat_page = TRUE;
		$stat_campaign = TRUE;
		$description = 'app1 register';
		$result = $this->_add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
	}

	function _add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign){
		$result = $this->audit_lib->add_audit_action($app_id, $action_id, $description, $stat_app, $stat_page, $stat_campaign);
		if($result){
			echo 'audit action added';
		}else{
			echo 'audit action add fail';
		}
		echo '$app_id: ' . $app_id . '<br/>';
		echo '$action_id: ' . $action_id . '<br/>';
		echo '$stat_app: ' . $stat_app . '<br/>';
		echo '$stat_page: ' . $stat_page . '<br/>';
		echo '$stat_campaign: ' . $stat_campaign . '<br/>';
		echo '$description: ' . $description . '<br/>';
	}
	
	
	/**
	 * platform visit
	 */
	function addlog01(){
		$app_id = 0;
		$action_id = 1;
		$subject = 'userX';
		$object = 'object';
		$objecti = 'objecti';
		$additional_data = array('app_install_id' => 0);
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		if($result) echo 'platform visit';
	}

	/**
	 * platform register
	 */
	function addlog02(){
		$app_id = 0;
		$action_id = 2;
		$subject = 'userX';
		$object = 'object';
		$objecti = 'objecti';
		$additional_data = array('app_install_id' => 0);
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		if($result) echo 'platform register';
	}
	
	/**
	 * app1 visit
	 */
	function addlog11(){
		$app_id = 1;
		$action_id = 1;
		$subject = 'userU';
		$object = 'object';
		$objecti = 'objecti';
		$additional_data = array('app_install_id' => rand(1, 20),
								'campaign_id' => rand(1, 30),
								'company_id' => rand(1, 20),
								'page_id' => rand(1, 20));
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		if($result) echo 'app1 visit';
	}
	
	/**
	 * app1 register
	 */
	function addlog12(){
		$app_id = 1;
		$action_id = 2;
		$subject = 'userX';
		$object = 'object';
		$objecti = 'objecti';
		$additional_data = array('app_install_id' => rand(1, 20),
								'campaign_id' => rand(1, 30),
								'company_id' => rand(1, 20),
								'page_id' => rand(1, 20));
		$result = $this->audit_lib->add_audit($app_id, $subject, $action_id, $object, $objecti, $additional_data);
		if($result) echo 'app1 register';
	}
	
	
	function addlog(){
		echo 'addlog';
		$rand = rand(1, 4) . '';
		//echo $rand;
		switch ($rand) {
		case '1':
			$this->addlog01();
		break;
		
		case '2':
			$this->addlog02();
		break;
			
		case '3':
			$this->addlog11();
		break;
			
		case '4':
			$this->addlog12();
		break;
			
		default:
		
		break;
		}
	}
	
	
	
	/**
	 * add new audit entry
	 */
	function add(){
		$audit = array('subject' => 'subject',
						'action' => '1',
						'object' => '2',
						'objecti' => '3',
						'type' => '4');
		$this->Audit->add_audit($audit);
		//echo 'added';
	}
	
	/**
	 * list recent audit
	 */
	function list_audit(){
		$audit_list = $this->audit_lib->list_recent_audit();
		foreach ($audit_list as $audit) {
			//echo $audit['subject'] . "<br/>";
			echo '<pre>' . print_r($audit) . '</pre>';
		}
	}
	
	/**
	 * JSON : get company activity log
	 * @author Prachya P.
	 */
	function json_get_company_activity_log($company_id){
		$this -> load -> model('audit_action_type_model', 'audit_type');
		$this -> load -> model('user_model', 'user');
		$this -> load -> model('page_model', 'page');
		$this -> load -> model('installed_apps_model', 'installed_app');
		$action_list=array(1,2,3,4,5);
		$action_list_name=array();
		$limit=5;
		$audit_list = $this->audit_lib->list_audit(
					array(
						'company_id'=>(int)($company_id),
						'action_id'=>array('$in' => $action_list)
					),$limit);
		foreach($action_list as $action_id){
			$audit_action=$this->audit_type->get_audit_action_by_type_id($action_id);
			$action_list_name[$action_id]=$audit_action['audit_action_name'];
		}
		foreach($audit_list as $key => $audit){
			$audit_list[$key]['action_name']=$action_list_name[$audit['action_id']];
			$user_profile=$this->user->get_user_profile_by_user_id($audit_list[$key]['subject']);
			$audit_list[$key]['image']=$user_profile['user_image'];
			$audit_list[$key]['user_first_name']=$user_profile['user_first_name'];
			$audit_list[$key]['user_id']=$user_profile['user_id'];
			if($audit['action_id']==1){
				$app_profile=$this->installed_app->get_app_profile_by_app_install_id($audit_list[$key]['app_install_id']);
				$audit_list[$key]['app_name']=$app_profile['app_name'];
				$audit_list[$key]['app_install_id']=$app_profile['app_install_id'];
			}
			else if($audit['action_id']==2){
				$app_profile=$this->installed_app->get_app_profile_by_app_install_id($audit_list[$key]['app_install_id']);
				$audit_list[$key]['app_name']=$app_profile['app_name'];
				$audit_list[$key]['app_install_id']=$app_profile['app_install_id'];
				$page_profile=$this->page->get_page_profile_by_page_id($audit_list[$key]['page_id']);
				$audit_list[$key]['page_name']=$page_profile['page_name'];
				$audit_list[$key]['page_id']=$app_profile['page_id'];
			}
			else if($audit['action_id']==5){
				$page_profile=$this->page->get_page_profile_by_page_id($audit_list[$key]['page_id']);
				$audit_list[$key]['page_name']=$page_profile['page_name'];
				$audit_list[$key]['page_id']=$page_profile['page_id'];
			}
			$audit_list[$key]['datetime']=date("d/m/Y H:i:s",$audit_list[$key]['timestamp']);
		}
		echo json_encode($audit_list);
	}

}

/* End of file audit.php */
/* Location: ./application/controllers/audit.php */