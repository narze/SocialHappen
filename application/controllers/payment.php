<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
	}
	
	/**
	 * Payment page
	 * @author Weerapat P.
	 */
	function index(){
		
	}
	
	/**
	 * Payment page
	 * @author Weerapat P.
	 */
	function payment_form()
	{
		$this->socialhappen->ajax_check();
		$this->socialhappen->check_logged_in( base_url().'home/facebook_connect?package_id='.set_value('package_id') );
		
		$this->load->model('package_model','packages');
		$user = $this->socialhappen->get_user();
		
		$this->form_validation->set_rules('package_id', 'Package ID', 'required|trim|xss_clean');			
		$this->form_validation->set_rules('payment_method', 'Payment method', 'required|trim|xss_clean|max_length[255]');
		/*
		if(set_value('payment_method') == 'credit_card') 
		{
			$this->form_validation->set_rules('credit_card_number', 'Credit card number', 'required|trim|xss_clean|max_length[12]');
			$this->form_validation->set_rules('credit_card_expire_month', 'Expire month', 'required|trim|xss_clean|max_length[2]');			
			$this->form_validation->set_rules('credit_card_expire_year', 'Expire year', 'required|trim|xss_clean|max_length[2]');
			$this->form_validation->set_rules('credit_card_csc', 'CSC', 'required|trim|xss_clean|max_length[3]');			
		}
		*/
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		$options = array();
		$packages = $this->packages->get_packages();
		foreach($packages as $package) {
			$price = $package['package_price'] ? ' ('.$package['package_price'].'THB' : ' (FREE)';
			$duration = $package['package_duration'] ? '/'.$package['package_duration'].')' : '';
			$options[$package['package_id']] = $package['package_name'].$price.$duration;
        }
		unset($package);
		
		
		if ($this->form_validation->run() == FALSE)
		{
			$selected_package = $this->packages->get_package_by_package_id($this->input->get('package_id'));
			if (!$selected_package) {
				$selected_package = $packages[0];
			}
			$this -> load -> view('payment/payment_form', 
					array(
						'packages' => $packages,
						'selected_package' => $selected_package,
						'options' => $options
					)
			);
		}
		else
		{
			$package = $this->packages->get_package_by_package_id(set_value('package_id'));
			$item_unit = 1;
			$item_discount = 0;
			$transaction_status = true;
			
			//add order
			$order = array(
		       	'order_id' => NULL,
				'order_date' => NULL,
				'order_status' => $this->socialhappen->get_k('order_status', 'Processed'),
				'order_net_price' => ($package['package_price'] * $item_unit) * (1-($item_discount/100)),
				'user_id' => $user['user_id'],
				'payment_method' => set_value('payment_method'),
				'billing_info' => serialize(
					array(
						'user_first_name' => $user['user_first_name'],
						'user_last_name' => $user['user_last_name'],
						'user_email' => $user['user_email'],
						'credit_card_number' => set_value('credit_card_number'),
						'credit_card_expire_month' => set_value('credit_card_expire_month'),
						'credit_card_expire_year' => set_value('credit_card_expire_year'),
						'credit_card_csc' => set_value('credit_card_csc')
					)
				)
			);
			$this->load->model('order_model','orders');
			if(!$order_id = $this->orders->add_order($order)) { $transaction_status = false; }
			
			//add order_items
			$order_item = array(
		       	'order_id' => $order_id,
		       	'item_id' => $package['package_id'],
		       	'item_type' => $this->socialhappen->get_k('item_type', 'Package'),
		       	'item_name' => $package['package_name'],
		       	'item_description' => $package['package_detail'],
		       	'item_price' => $package['package_price'],
		       	'item_unit' => $item_unit,
		       	'item_discount' => $item_discount
			);
			$this->load->model('order_items_model','order_items');
			if(!$this->order_items->add_order_item($order_item)) { $transaction_status = false; }
			
			//add package_users
			$package_user = array(
				'package_id' => $package['package_id'],
				'user_id' => $user['user_id'],
				'package_expire' => date('Y-m-d H:i:s', strtotime('+'.$package['package_duration']))
			);
			$this->load->model('package_users_model','package_users');
			if($this->package_users->get_package_by_user_id($user['user_id']))
			{
				$add_package_users_result = $this->package_users->update_package_user_by_user_id($user['user_id'], $package_user);
			}
			else
			{
				$add_package_users_result = $this->package_users->add_package_user($package_user);
			}
			if(!$add_package_users_result) { $transaction_status = false; }
			
			//Return sesult
			if ($transaction_status)
			{	
				$data = array(
					'order' => array(
						'billing_info' => $user['user_first_name'].' '.$user['user_last_name'],
						'package' => $package['package_name'],
						'payment_method' => ucfirst(str_replace('_', ' ', $order['payment_method'])),
						'order_net_price' => $order['order_net_price']
					)
				);
				$this->load->view('payment/payment_confirm',$data);
			}
			else
			{
				echo 'Error occured';
			}
		}
	
	}
}

/* End of file payment.php */
/* Location: ./application/controllers/payment.php */