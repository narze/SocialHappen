<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {
	
	/**
	 * job timeout in second
	 */
	var $time_out;
	
	/**
	 * time interval between active_user cron
	 */
	var $cron_active_user_time_interval;
	
	/**
	 * time interval between active_user cron for day
	 */
	var $cron_active_user_time_interval_day;
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('Cron_job_model', 'Cron_job');
		$this->time_out = '30'; // 30 sec
		$this->cron_active_user_time_interval = '3600'; // : one hour
		$this->cron_active_user_time_interval_day = '86400'; // : one day
	}
	
	/**
	 * index of cron !
	 */
	function index()
	{
		// cron for one hour activity
		$this->cron_active_user(1, $this->cron_active_user_time_interval);
		
		// cron for one day activity
		$this->cron_active_user(2, $this->cron_active_user_time_interval_day);
	}
	
	
	function cron(){
		$response = array('status' => 'OK');	
		$this->load->model('User_apps_model', 'User_apps');
		$response['active_user'] = $this->User_apps->get_user_apps_in_time_range($this->cron_active_user_time_interval);
		echo json_encode($response);
	}
	
	/**
	 * validate time condition for cron active user
	 */
	function cron_active_user($job_id, $time_interval){
		$response = array('status' => 'OK');
		$this->load->model('User_apps_model', 'User_apps');

		$now = time();
		
		$job = $this->Cron_job->get_job_by_id($job_id);
		$response['now'] = date ("Y-m-d H:i:s", $now);
		
		if($job->job_status == 'running' && $job->job_start >= date ("Y-m-d H:i:s", $now - $this->time_out)){
			$response['message'] = 'old job is running and not time out';
			$response['old_job_start'] = $job->job_start;
			$response['old_job_status'] = $job->job_status;
			echo json_encode($response);
			return;
		}else if($job->job_status == 'running' && $job->job_start < date ("Y-m-d H:i:s", $now - $this->time_out)){
			//$response['message'] = 'old job is running but time out';
			//$response['old_job_start'] = $job->job_start;
			//$response['old_job_status'] = $job->job_status;
			//echo json_encode($response);
			//return;
		}else if($job->job_status == 'finish' && $job->job_start > date ("Y-m-d H:i:s", $now - $time_interval)){
			$response['message'] = 'old job is finish but it\'s not time to start new job';
			$response['old_job_start'] = $job->job_start;
			$response['old_job_finish'] = $job->job_finish;
			$response['new_job_will_start'] = date ("Y-m-d H:i:s", strtotime($job->job_finish) + $time_interval);
			$response['time_to_start'] = (strtotime($job->job_finish) + $time_interval - $now) . ' sec';
			$response['old_job_status'] = $job->job_status;
			echo json_encode($response);
			return;
		}
		
		// start job
		$response[] = $this->_do_cron_active_user($job_id, $now, $job);
		$response['job'] = 'DONE';
		echo json_encode($response);
	}
	
	/**
	 * do cron job
	 */
	function _do_cron_active_user($job_id, $now, $job){
		$old_job_start = $job->job_start;
		
		// start job
		$this->Cron_job->start_job($job_id);
		
		// time interval to grab active user
		$time_interval = $now - strtotime($job->job_finish);
		
		// get all user app
		$active_user_list = $this->User_apps->get_user_apps_in_time_range($time_interval);
		
		// group by app_install_id
		$app_install_list = array();
		foreach ($active_user_list as $user) {
			$app_install_list[$user->app_install_id] = isset($app_install_list[$user->app_install_id]) ? $app_install_list[$user->app_install_id] + 1 : 1;
			//echo $user->app_install_id;
		}
		
		$this->load->model('App_statistic_model', 'App_statistic');
		foreach ($app_install_list as $key => $value) {
			$this->App_statistic->add(array('app_install_id' => $key,
											'job_time' => date ("Y-m-d H:i:s", $now),
											'job_id' => $job_id,
											'active_user' => $value));
		}
		
		
		// finish job
		$this->Cron_job->finish_job($job_id);
		return $app_install_list;
	}
	
	/**
	 * create new cron job
	 */
	function new_cron_job(){
		$job_name = 'active_user_check';
		$this->Cron_job->add(array('job_name' => $job_name,
									'job_status' => 'finish'));
	}
}

/* End of file cron.php */
/* Location: ./application/controllers/cron.php */