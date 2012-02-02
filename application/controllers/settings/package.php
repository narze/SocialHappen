<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Package extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->socialhappen->check_logged_in();
	}
	
	function index($user_id = NULL){
		$this->load->library('settings');
		$setting_name = 'package';
		$this->settings->view_settings($setting_name, $user_id, NULL);
	}
	
	function view($user_id = NULL){
		//$this->socialhappen->ajax_check();
		if($user_id && $user_id == $this->socialhappen->get_user_id())
		{
			//Get all orders
			$this->load->model('order_model','orders');
			$orders = $this->orders->get_orders_by_user_id($user_id, $limit = NULL, $offset = NULL);
			$this->load->model('order_items_model','order_items');
			foreach($orders as &$order)
			{
				$items = $this->order_items->get_order_items_by_order_id($order['order_id']);
				$order['package_name'] = isset($items[0]['item_name']) ? $items[0]['item_name'] : '-';
			}
			arsort($orders); //reverse order. sort by desc
			
			//Get current package
			$this->load->model('package_users_model','package_users');
			$current_package = $this->package_users->get_package_by_user_id($user_id);
			
			//Get package apps
			$this->load->model('package_apps_model','package_apps');
			$apps = $this->package_apps->get_apps_by_package_id($current_package['package_id']);
			
			//Count user companies
			$this->load->model('company_model','companies');
			$user_companies = $this->companies->get_companies_by_user_id($user_id);
			
			//Count user pages
			$this->load->model('user_pages_model','user_pages');
			$user_pages = $this->user_pages->get_user_pages_by_user_id($user_id);
			
			//Count members
			$members = $this->package_users->count_user_members_by_user_id($user_id);
			
			//is upgradable?
			$this->load->model('package_model','package');
			$is_upgradable = $this->package->is_upgradable($current_package['package_id']);
			
			$data = array(
				'user_id' => $user_id,
				'orders' => $orders,
				'current_package' => $current_package,
				'user_companies' => count($user_companies),
				'user_pages' => count($user_pages),
				'members' => $members,
				'apps' => $apps,
				'is_upgradable' => $is_upgradable
			);
			$this->load->view('settings/package',$data);
		}
	}
}
/* End of file package.php */
/* Location: ./application/controllers/settings/package.php */