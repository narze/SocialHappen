<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		// Load PayPal library
		$this->config->load('paypal_pro');
		
		$config = array(
			'APIUsername' => $this->config->item('APIUsername'), 	// PayPal API username of the API caller
			'APIPassword' => $this->config->item('APIPassword'), 	// PayPal API password of the API caller
			'APISignature' => $this->config->item('APISignature'), 	// PayPal API signature of the API caller
			'APISubject' => '', 									// PayPal API subject (email address of 3rd party user that has granted API permission for your app)
			'APIVersion' => $this->config->item('APIVersion')		// API version you'd like to use for your call.  You can set a default version in the class and leave this blank if you want.
		);
		
		$this->load->library('paypal_pro', $config);	
	}
	
	/**
	 * Payment page
	 * @author Weerapat P.
	 */
	function index()
	{
		
	}
	
	/**
	 * Payment page
	 * @author Weerapat P.
	 */
	function payment_form()
	{
		$this->socialhappen->ajax_check();
		$this->socialhappen->check_logged_in( base_url().'home/facebook_connect?package_id='.set_value('package_id') );
		
		$this->form_validation->set_rules('package_id', 'Package ID', 'required|trim|xss_clean');
		$this->form_validation->set_rules('payment_method', 'Payment method', 'trim|xss_clean|max_length[255]');
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
		
		$this->load->model('package_model','packages');
		$this->load->model('package_users_model','package_users');
		$user = $this->socialhappen->get_user();
		$user_current_package = $this->package_users->get_package_by_user_id($user['user_id']);
		$user_current_package_id = isset($user_current_package['package_id']) ? $user_current_package['package_id'] : 0;
		$user_current_package_price = isset($user_current_package['package_price']) ? $user_current_package['package_price'] : 0;
		
		if ($this->form_validation->run() == FALSE) //
		{
			$packages = $this->packages->get_packages();
			
			if($this->input->get('package_id') > 0)
			{
				$selected_package = $this->packages->get_package_by_package_id($this->input->get('package_id'));
			}
			else
			{
				$selected_package = $this->packages->get_package_by_package_id($packages[0]['package_id']);
			}
			
			$options = array();
			$buyable_packages = array();
			if($packages) 
			{
				foreach($packages as &$package) 
				{
					if($package['package_price'] == 0) $free_package_id = $package['package_id'];
					$price = $package['package_price'] ? number_format($package['package_price']).'USD' : 'FREE';
					$duration = $package['package_duration'] == 'unlimited' ? '' : '/'.$package['package_duration'] ;

					if($user_current_package_id)
					{
						if($package['package_price'] > $user_current_package_price) //Show only package that user can buy
						{
							$options[$package['package_id']] = $package['package_name'].' <span>('.$price.$duration.')</span>';
							$buyable_packages[] = $package;
						}
						else 
						{
							unset($package);
						}
					}
					else //User have no package
					{
						$options[$package['package_id']] = $package['package_name'].' <span>('.$price.$duration.')</span>';
						$buyable_packages[] = $package;
					}
				}	
			}

			$this->load->view('payment/payment_form', 
					array(
						'packages' => $buyable_packages,
						'selected_package' => $selected_package,
						'options' => $options,
						'free_package_id' => isset($free_package_id)
					)
			);
		}
		else //Pass validation
		{
			$package = $this->packages->get_package_by_package_id(set_value('package_id'));
			$item_unit = 1;
			$item_discount = 0;
			
			// 1. Add order
			$order = array(
		       	'order_id' => NULL,
				'order_date' => NULL,
				'order_status_id' => NULL,
				'order_net_price' => ($package['package_price'] * $item_unit) * (1-($item_discount/100)),
				'user_id' => $user['user_id'],
				'payment_method' => NULL,
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
			if($order['order_net_price'] > 0) // Comercial package
			{
				$order['payment_method'] = set_value('payment_method');
				$order['order_status_id'] = $this->socialhappen->get_k('order_status', 'Pending');
			}
			else // Free package
			{
				$order['payment_method'] = '';
				$order['order_status_id'] = $this->socialhappen->get_k('order_status', 'Processed');
				//Get user first company
				$this->load->model('company_model','company');
				$user_own_companies = $this->company->get_companies_by_user_id($user['user_id']);
				$user_first_company = $user_own_companies[0]; //TODO : user maybe has no company (reg from facebook)
			}
			$this->load->model('order_model','orders');
			if(!$order['order_id'] = $this->orders->add_order($order)) {
				log_message('error','add order failed');
				echo json_encode(array(
					'status'=>'ERROR',
					'msg'=>'Error while adding order.' 
				));
				return false; 
			}
			
			// 2. Add order_items
			$order_items = array(
				array(
					'order_id' => $order['order_id'],
					'item_id' => $package['package_id'],
					'item_type_id' => $this->socialhappen->get_k('item_type', 'Package'),
					'item_name' => $package['package_name'],
					'item_description' => $package['package_detail'],
					'item_price' => $package['package_price'],
					'item_unit' => $item_unit,
					'item_discount' => $item_discount
				)
			);
			$this->load->model('order_items_model','order_items');
			foreach ($order_items as $order_item)
			{
				if(!$this->order_items->add_order_item($order_item)) { 
					log_message('error','add order item failed');
					echo json_encode(array(
						'status'=>'ERROR', 
						'msg'=>'Error while adding item to order.' 
					)); 
					return false; }
			}
			
			// 3. Add package_user and company_apps
			if($order['order_net_price'] > 0) // Comercial package
			{
				//Add after payment complete.
			}
			else // Free package
			{
				// 3.1. Add package_users
				$add_package_user_result = $this->_add_package_user($user['user_id'], $package['package_id']);
				if(!$add_package_user_result) 
				{ 
					log_message('error','add package user failed');
					echo json_encode(array('status'=>'ERROR', 'msg'=>'Error while adding package.' )); return false;
				}
				// 3.2. Add/Update company_apps
				$add_company_app_result = $this->_add_company_app($user['user_id'], $package['package_id']);
				if(!$add_company_app_result) 
				{ 
					echo json_encode(array('status'=>'ERROR', 'msg'=>'Error while adding app.' )); return false;
				}
			}
			
			//Return result
			$data = array(
				'user' => $user,
				'order' => $order,
				'order_items' => $order_items
			);

			switch($order['payment_method'])
			{
				//case 'bank_transfer': break;
				//case 'counter_service': break;
				case 'credit_card':
				case 'paypal':
					$PayPalResult = $this->_set_express_checkout($data);
					if($PayPalResult['ERRORS']) 
					{
						log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
						echo json_encode(array('status'=>'ERROR', 'msg'=> implode('<br />', $PayPalResult['ERRORS']) ));
						return false;
					}
					else
					{
						$url = $PayPalResult['REDIRECTURL'];
					}
					break;
				default : //Free package, redirect to first company
					$url = base_url().'company/'.$user_first_company['company_id'].'?popup=thanks';
					break;
			}
			echo json_encode(array('status'=>'OK', 'msg'=>'Redirect', 'url'=> $url ));
		}
	}
	
	/**
	 * Paypal payment summary page (Redirect from paypal site)
	 * @author Weerapat P.
	 * @param $order_id
	 */
	function payment_summary_paypal($order_id = NULL)
	{
		$view_data = array(
				'header' => $this -> socialhappen -> get_header( 
					array(
						'title' => 'Payment Summary',
						'script' => array(
							'common/functions',
							'common/jquery.form',
							'common/bar',
							'common/fancybox/jquery.fancybox-1.3.4.pack',
							'home/lightbox',
							'payment/payment'
						),
						'style' => array(
							'common/platform',
							'common/main',
							'common/fancybox/jquery.fancybox-1.3.4'
						)
					)
				),
				'breadcrumb' => $this -> load -> view('common/breadcrumb', 
					array(
						'breadcrumb' => array( 
							'Payment Summary' => NULL
						)
					),
				TRUE),
				'footer' => $this -> socialhappen -> get_footer()
 		);

		$this->load->model('order_model','orders');
		$order = $this->orders->get_order_by_order_id($order_id);
		$order['billing_info'] = unserialize($order['billing_info']);
		
		$this->load->model('order_items_model','order_items');
		$order_items = $this->order_items->get_order_items_by_order_id($order_id);
		
		$this->load->model('user_model','users');
		$user = $this->users->get_user_profile_by_user_id($order['user_id']);
		
		$data = array(
			'token' => $this->input->get('token'),
			'payerid' => $this->input->get('PayerID'),
			'order' => $order,
			'order_items' => $order_items,
			'user' => $user
		);
		
		// 1. Get express checkout details
		$PayPalResult = $this->_get_express_checkout_details($data);
		if($PayPalResult['ERRORS']) 
		{
			log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
			$view_data['payment_body'] = $this->load->view('payment/payment_error', array('error' => 'Error while getting checkout details'),TRUE);
			$this->parser->parse('payment/payment_view', $view_data);
			return false;
		}
		$order['billing_info']['payer_id'] = issetor($PayPalResult['PAYERID']);
		unset($PayPalResult);
		
		// 2. Do express checkout payment
		$PayPalResult = $this->_do_express_checkout_payment($data);
		if($PayPalResult['ERRORS'])
		{
			log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
			$view_data['payment_body'] = $this->load->view('payment/payment_error', array('error' => 'Error while checking out.'),TRUE);
			$this->parser->parse('payment/payment_view', $view_data);
			return false;
		}
		unset($PayPalResult);
		
		// 3. Cancel exist paypal recurrent payment
		$profileid = $this->orders->get_latest_paypal_profile_id_by_user_id($user['user_id']);
		if($profileid)
		{
			//$current_recurring_payment = $this->_get_recurring_payments_profile_details($profileid);
			$cancel_data = array(
				'profileid' => $profileid,
				'action' => 'Cancel', 	// Cancel, Suspend, Reactivate
				'note' => 'Upgrade package'
			);
			$PayPalResult = $this->_manage_recurring_payments_profile_status($cancel_data);
			
			if($PayPalResult['ERRORS']) 
			{ 			
				log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
				$view_data['payment_body'] = $this->load->view('payment/payment_error', array('error' => 'Error while canceling paypal recurring payments profile.'),TRUE);
				$this->parser->parse('payment/payment_view', $view_data);
				return false;
			}
		}
		
		// 4. Create recurring payments profile
		$data['schedule_details'] = array(
				'desc' => $order_items[0]['item_description'],
				'maxfailedpayments' => 3,
				'autobillamt' => 'AddToNextBilling'
			);
		$data['billing_period'] = array(
				'billingperiod' => 'Month', // Month
				'billingfrequency' => 1, //1 = monthly , 2 = every2months
				'totalbillingcycles' => 0, //0 = until canceled
				'amt' => $order['order_net_price'], //Price
				'currencycode' => 'USD'
			);
		$PayPalResult = $this->_create_recurring_payments_profile($data);
		if($PayPalResult['ERRORS']) 
		{ 			
			log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
			$view_data['payment_body'] = $this->load->view('payment/payment_error', array('error' => 'Error while creating paypal recurring payments profile.'),TRUE);
			$this->parser->parse('payment/payment_view', $view_data);
			return false;
		}

		// 5. Update order status to "Processed" and Update billing info form paypal
		$order['billing_info']['profile_id'] = issetor($PayPalResult['PROFILEID']);
		$order['billing_info']['profile_status'] = issetor($PayPalResult['PROFILESTATUS']);
		$order['billing_info']['txn_id'] = issetor($PayPalResult['PAYMENTS'][0]['TRANSACTIONID']);
		$order['billing_info']['payment_status'] = issetor($PayPalResult['PAYMENTS'][0]['PAYMENTSTATUS']);
		$order['billing_info']['pending_reason'] = issetor($PayPalResult['PAYMENTS'][0]['PENDINGREASON']);
		$order['billing_info']['reason_code'] = issetor($PayPalResult['PAYMENTS'][0]['REASONCODE']);
		$update_data = array(
			'order_status_id' => $this->socialhappen->get_k('order_status', 'Processed'),
			'billing_info' => serialize($order['billing_info'])
		);
		if(!$this->orders->update_order_by_order_id($order['order_id'], $update_data))
		{
			log_message('error','update order failed');
			$view_data['payment_body'] = $this->load->view('payment/payment_error', array('error' => 'Error while updating order status.'),TRUE);
			$this->parser->parse('payment/payment_view', $view_data);
			return false;
		}
		
		// 6. Add package_users
		$add_package_user_result = $this->_add_package_user($user['user_id'], $order_items[0]['item_id']);
		if(!$add_package_user_result) 
		{ 
			log_message('error','add package user failed');
			$view_data['payment_body'] = $this->load->view('payment/payment_error', array('error' => 'Error while adding package user.'),TRUE);
			$this->parser->parse('payment/payment_view', $view_data);
			return false;
		}
		
		// 7. Add/Update company_apps
		$add_company_app_result = $this->_add_company_app($user['user_id'], $order_items[0]['item_id']);
		if(!$add_company_app_result) 
		{ 
			$view_data['payment_body'] = $this->load->view('payment/payment_error', array('error' => 'Error while adding company app.'),TRUE);
			$this->parser->parse('payment/payment_view', $view_data);
			return false;
		}
		
		//8. Payment complete
		$view_data['header'] = $this->socialhappen->get_header( 
					array(
						'title' => 'Payment Summary',
						'script' => array(
							'home/lightbox',
							'payment/payment'
						),
						'vars' => array(
							'popup_name' => 'payment/payment_complete/'.$order_items[0]['item_id']
						)
					)
				);
		$view_data['payment_body'] = $this->load->view('payment/payment_summary_paypal', array(
			'order' => array(
				'order_id' => $order['order_id'],
				'billing_info' => $order['billing_info']['user_first_name'].' '.$order['billing_info']['user_last_name'],
				'package' => $order_items[0]['item_name'],
				'payment_method' => ucfirst(str_replace('_', ' ', $order['payment_method'])),
				'order_net_price' => $order['order_net_price']
			)
		)
		,TRUE);

		$this->parser->parse('payment/payment_view', $view_data);
	}
	
	/**
	 * Payment complete popup
	 * @author Weerapat P.
	 * Redirect from paypal site
	 */
	function payment_complete($package_id)
	{
		$this->socialhappen->ajax_check();
		$this->load->model('package_model','packages');
		$data = array(
			'package' => $this->packages->get_package_by_package_id($package_id)
		);
		$this->load->view('payment/payment_complete', $data);
	}
	
	/**
	 * Billing detail
	 * @author Weerapat P.
	 */
	function invoice($order_id = NULL)
	{
		if(!$order_id) return false;
		
		$user = $this->socialhappen->get_user();
		$this->load->model('order_model','orders');				
		$order = $this->orders->get_order_by_order_id($order_id);
		
		if(!$order) echo 'Order not found.';
		
		//allow only buyer
		if($user['user_id'] != $order['user_id']) return false;
		
		$this->load->model('order_items_model','order_items');	
		$items = $this->order_items->get_order_items_by_order_id($order['order_id']);
		$order['package_name'] = isset($items[0]['item_name']) ? $items[0]['item_name'] : '-';
		
		$this->load->vars(array(
				'user' => $user,
				'order' => $order,
				'print_view' => $this->input->get('action') == 'print' ? TRUE : FALSE 
			)
		);
		
		$this->load->view('payment/invoice');
	}
	
	/**
	 * Cancel package
	 * @author Weerapat P.
	 */
	function cancel_package($user_id)
	{
		$this->load->model('order_model','orders');
		$this->load->model('package_model','package');
		$this->load->model('package_users_model','package_users');
		
		$item_type_id = $this->socialhappen->get_k('item_type', 'Package');
		$latest_ordered_package = $this->orders->get_latest_ordered_by_user_id_and_item_type_id($user_id, $item_type_id);
		$error = array();
		
		//Switch to Free package
		$free_package = $this->package->get_free_package();
		$data = array('package_id'=>$free_package['package_id'], 'package_expire'=>date('Y-m-d H:i:s'));
		if(!$this->package_users->update_package_user_by_user_id($latest_ordered_package['user_id'], $data)) $error[] = 'Can not switch to free package';

		//Cancel paypal recurring payment
		if(isset($latest_ordered_package['billing_info']['profile_id']))
		{
			$data = array(
				'profileid' => $latest_ordered_package['billing_info']['profile_id'], 	// Required. Recurring payments profile ID returned from CreateRecurring...
				'action' => 'Cancel', 	// Cancel, Suspend, Reactivate
				'note' => 'User cancel package'
			);

			$PayPalResult = $this->_manage_recurring_payments_profile_status($data);
			if($PayPalResult['ERRORS']) 
			{ 	
				log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
				$error[] = 'Can not cancel paypal recurring payment';
				return false;
			}
		}
		
		//Change order status
		$data = array('order_status_id' => $this->socialhappen->get_k('order_status', 'Canceled'));
		if(!$this->orders->update_order_by_order_id($latest_ordered_package['order_id'], $data)) $error[] = 'Error. Can not update order status';
		
		if(count($error)) { 
			log_message('error','cancel package error : '.print_r($error));
			echo '<pre>'; print_r($error); echo '</pre>'; 
		}
		else {  redirect(); }
	}
	
	/**
	 * Recieve paypal notifications
	 * @author Weerapat P.
	 */
	function paypal_listener()
	{		
		if(defined('ENVIRONMENT') && ENVIRONMENT == 'production') {
			if (!in_array($_SERVER['REMOTE_ADDR'], array('216.113.188.202','216.113.188.203','216.113.188.204','66.211.170.66'))) { header("HTTP/1.0 404 Not Found"); exit(); }
		}
		
		$response = $_REQUEST;
		switch(strtolower($response['payment_status']))
		{
			case 'canceled_reversal' : //Canceled_Reversal: A reversal has been canceled. For example, you won a dispute with the customer, and the funds for the transaction that was reversed have been returned to you.
				break;
			case 'completed' : //Completed: The payment has been completed, and the funds have been added successfully to your account balance.
				break;
			case 'created' : //Created: A German ELV payment is made using Express Checkout.
				break;
			case 'denied' : //Denied: You denied the payment. This happens only if the payment was previously pending because of possible reasons described for the pending_reason variable or the Fraud_Management_Filters_x variable.
				break;
			case 'expired' : //Expired: This authorization has expired and cannot be captured.
				break;
			case 'failed' : //Failed: The payment has failed. This happens only if the payment was made from your customer’s bank account.
				break;
			case 'pending' : //Pending: The payment is pending. See pending_reason for more information.
				break;
			case 'refunded' : //Refunded: You refunded the payment.
				
				$this->load->model('order_model','orders');
				$this->load->model('package_model','package');
				$this->load->model('package_users_model','package_users');
				
				$order = $this->orders->get_order_by_txn_id($response['txn_id']);
				
				$result = true;
				
				//Switch to Free package
				$free_package = $this->package->get_free_package();
				$data = array('package_id'=>$free_package['package_id'], 'package_expire'=>date('Y-m-d H:i:s'));
				if(!$this->package_users->update_package_user_by_user_id($order['user_id'], $data)) $result = false;
				
				//Change order status
				$data = array('order_status_id' => $this->socialhappen->get_k('order_status', 'Refunded'));
				if(!$this->orders->update_order_by_order_id($order['order_id'], $data)) $result = false;
				
				if(!$result) { } //Do something
				break;
				
			case 'reversed' : //Reversed: A payment was reversed due to a chargeback or other type of reversal. The funds have been removed from your account balance and returned to the buyer. The reason for the reversal is specified in the ReasonCode element.
				break;
			case 'processed' : //Processed: A payment has been accepted.
				break;
			case 'voided' : //Voided: This authorization has been voided.
				break;
			default : break;
		}
	}
	
	function _add_package_user($user_id, $package_id)
	{		
		$this->load->model('package_model','packages');
		$this->load->model('package_users_model','package_users');
		$package = $this->packages->get_package_by_package_id($package_id);
		$user_current_package = $this->package_users->get_package_by_user_id($user_id);
		$package_user = array(
			'package_id' => $package['package_id'],
			'user_id' => $user_id,
			'package_expire' => $package['package_duration'] == 'unlimited' ? '0' : date('Y-m-d H:i:s', strtotime('+'.$package['package_duration']))
		);
		
		$result = NULL;
		if(count($user_current_package)) //if user already have one package
		{
			$result = $this->package_users->update_package_user_by_user_id($user_id, $package_user);
		}
		else
		{
			$result = $this->package_users->add_package_user($package_user);
		}
		
		if($result){
			$this->load->library('audit_lib');
			if($this->packages->is_the_most_expensive($package_id)){
				$action_id = $this->socialhappen->get_k('audit_action','Buy Most Expensive Package');
			} else {
				$action_id = $this->socialhappen->get_k('audit_action','Buy Package');
			}
			$this->audit_lib->add_audit(
				0,
				$user_id,
				$action_id,
				'', 
				'',
				array(
					'app_install_id' => 0,
					'user_id' => $user_id
				)
			);
			
			$this->load->library('achievement_lib');
			$info = array('action_id'=> $action_id, 'app_install_id'=>0);
			$stat_increment_result = $this->achievement_lib->increment_achievement_stat(0, $user_id, $info, 1);
		}
		return $result;
	}
	
	function _add_company_app($user_id, $package_id)
	{
		/* 1.Find companies that user is admin
		*  2.For each company remove old company apps
		*  3.For each company add new company apps
		*/
		$this->load->model('user_companies_model','user_companies');
		$this->load->model('company_apps_model','company_apps');
		$this->load->model('package_apps_model','package_apps');
		$user_companies = $this->user_companies->get_user_companies_by_user_id($user_id);
		foreach($user_companies as $company)
		{
			if($company['user_role'] != 1) { //Not company admin
				continue;
			}
			$company_id = $company['company_id'];
			$this->company_apps->remove_company_apps($company_id);
			$package_apps = $this->package_apps->get_package_apps_by_package_id($package_id);
			foreach($package_apps as $package_app)
			{
				$result = $this->company_apps->add_company_app(array(
					'company_id' => $company_id,
					'app_id' => $package_app['app_id']
				));
				
				if(!$result) { 
					log_message('error','add compoany apps failed');
					return false; 
				}
			}
		}
		return true;
	}
	
	/**
	 * Set express checkout
	 * @author Weerapat P.
	 * @param $data
	 * Called by payment_form()
	 */
	function _set_express_checkout($data)
	{
		$user = $data['user'];
		$order = $data['order'];
		$order_items = $data['order_items'];
		
		$SECFields = array(
							'token' => '', 								// A timestamped token, the value of which was returned by a previous SetExpressCheckout call.
							'maxamt' => '', 						// The expected maximum total amount the order will be, including S&H and sales tax.
							'returnurl' => base_url().'payment/payment_summary_paypal/'.$order['order_id'], 							// Required.  URL to which the customer will be returned after returning from PayPal.  2048 char max.
							'cancelurl' => base_url().'home/package', 							// Required.  URL to which the customer will be returned if they cancel payment on PayPal's site.
							'callback' => '', 							// URL to which the callback request from PayPal is sent.  Must start with https:// for production.
							'callbacktimeout' => '', 					// An override for you to request more or less time to be able to process the callback request and response.  Acceptable range for override is 1-6 seconds.  If you specify greater than 6 PayPal will use default value of 3 seconds.
							'callbackversion' => '', 					// The version of the Instant Update API you're using.  The default is the current version.							
							'reqconfirmshipping' => '', 				// The value 1 indicates that you require that the customer's shipping address is Confirmed with PayPal.  This overrides anything in the account profile.  Possible values are 1 or 0.
							'noshipping' => '', 						// The value 1 indiciates that on the PayPal pages, no shipping address fields should be displayed.  Maybe 1 or 0.
							'allownote' => '', 							// The value 1 indiciates that the customer may enter a note to the merchant on the PayPal page during checkout.  The note is returned in the GetExpresscheckoutDetails response and the DoExpressCheckoutPayment response.  Must be 1 or 0.
							'addroverride' => '', 						// The value 1 indiciates that the PayPal pages should display the shipping address set by you in the SetExpressCheckout request, not the shipping address on file with PayPal.  This does not allow the customer to edit the address here.  Must be 1 or 0.
							'localecode' => '', 						// Locale of pages displayed by PayPal during checkout.  Should be a 2 character country code.  You can retrive the country code by passing the country name into the class' GetCountryCode() function.
							'pagestyle' => '', 							// Sets the Custom Payment Page Style for payment pages associated with this button/link.  
							'hdrimg' => '', 							// URL for the image displayed as the header during checkout.  Max size of 750x90.  Should be stored on an https:// server or you'll get a warning message in the browser.
							'hdrbordercolor' => '', 					// Sets the border color around the header of the payment page.  The border is a 2-pixel permiter around the header space.  Default is black.  
							'hdrbackcolor' => '', 						// Sets the background color for the header of the payment page.  Default is white.  
							'payflowcolor' => '', 						// Sets the background color for the payment page.  Default is white.
							'skipdetails' => '', 						// This is a custom field not included in the PayPal documentation.  It's used to specify whether you want to skip the GetExpressCheckoutDetails part of checkout or not.  See PayPal docs for more info.
							'email' => $user['user_email'], 							// Email address of the buyer as entered during checkout.  PayPal uses this value to pre-fill the PayPal sign-in page.  127 char max.
							'solutiontype' => '', 						// Type of checkout flow.  Must be Sole (express checkout for auctions) or Mark (normal express checkout)
							'landingpage' => '', 						// Type of PayPal page to display.  Can be Billing or Login.  If billing it shows a full credit card form.  If Login it just shows the login screen.
							'channeltype' => '', 						// Type of channel.  Must be Merchant (non-auction seller) or eBayItem (eBay auction)
							'giropaysuccessurl' => '', 					// The URL on the merchant site to redirect to after a successful giropay payment.  Only use this field if you are using giropay or bank transfer payment methods in Germany.
							'giropaycancelurl' => '', 					// The URL on the merchant site to redirect to after a canceled giropay payment.  Only use this field if you are using giropay or bank transfer methods in Germany.
							'banktxnpendingurl' => '',  				// The URL on the merchant site to transfer to after a bank transfter payment.  Use this field only if you are using giropay or bank transfer methods in Germany.
							'brandname' => 'SocialHappen', 							// A label that overrides the business name in the PayPal account on the PayPal hosted checkout pages.  127 char max.
							'customerservicenumber' => '', 				// Merchant Customer Service number displayed on the PayPal Review page. 16 char max.
							'giftmessageenable' => '', 					// Enable gift message widget on the PayPal Review page. Allowable values are 0 and 1
							'giftreceiptenable' => '', 					// Enable gift receipt widget on the PayPal Review page. Allowable values are 0 and 1
							'giftwrapenable' => '', 					// Enable gift wrap widget on the PayPal Review page.  Allowable values are 0 and 1.
							'giftwrapname' => '', 						// Label for the gift wrap option such as "Box with ribbon".  25 char max.
							'giftwrapamount' => '', 					// Amount charged for gift-wrap service.
							'buyeremailoptionenable' => '', 			// Enable buyer email opt-in on the PayPal Review page. Allowable values are 0 and 1
							'surveyquestion' => '', 					// Text for the survey question on the PayPal Review page. If the survey question is present, at least 2 survey answer options need to be present.  50 char max.
							'surveyenable' => '', 						// Enable survey functionality. Allowable values are 0 and 1
							'buyerid' => '', 							// The unique identifier provided by eBay for this buyer. The value may or may not be the same as the username. In the case of eBay, it is different. 255 char max.
							'buyerusername' => '', 						// The user name of the user at the marketplaces site.
							'buyerregistrationdate' => '',  			// Date when the user registered with the marketplace.
							'allowpushfunding' => ''					// Whether the merchant can accept push funding.  0 = Merchant can accept push funding : 1 = Merchant cannot accept push funding.			
						);
		
		// Basic array of survey choices.  Nothing but the values should go in here.  
		$SurveyChoices = array('Choice 1', 'Choice2', 'Choice3', 'etc');
		
		// You can now utlize parallel payments (split payments) within Express Checkout.
		// Here we'll gather all the payment data for each payment included in this checkout 
		// and pass them into a $Payments array.  
		
		// Keep in mind that each payment will ahve its own set of OrderItems
		// so don't get confused along the way.
		$Payments = array();
		$Payment = array(
						'amt' => $order['order_net_price'], 							// Required.  The total cost of the transaction to the customer.  If shipping cost and tax charges are known, include them in this value.  If not, this value should be the current sub-total of the order.
						'currencycode' => 'USD', 					// A three-character currency code.  Default is USD.
						'itemamt' => '', 						// Required if you specify itemized L_AMT fields. Sum of cost of all items in this order.  
						'shippingamt' => '', 					// Total shipping costs for this order.  If you specify SHIPPINGAMT you mut also specify a value for ITEMAMT.
						'shipdiscamt' => '', 				// Shipping discount for this order, specified as a negative number.
						'insuranceoptionoffered' => '', 		// If true, the insurance drop-down on the PayPal review page displays the string 'Yes' and the insurance amount.  If true, the total shipping insurance for this order must be a positive number.
						'handlingamt' => '', 					// Total handling costs for this order.  If you specify HANDLINGAMT you mut also specify a value for ITEMAMT.
						'taxamt' => '', 						// Required if you specify itemized L_TAXAMT fields.  Sum of all tax items in this order. 
						'desc' => '', 							// Description of items on the order.  127 char max.
						'custom' => '', 						// Free-form field for your own use.  256 char max.
						'invnum' => '', 						// Your own invoice or tracking number.  127 char max.
						'notifyurl' => base_url().'payment/paypal_listener', 						// URL for receiving Instant Payment Notifications
						'shiptoname' => '', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
						'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
						'shiptostreet2' => '', 					// Second street address.  100 char max.
						'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
						'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
						'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
						'shiptocountrycode' => '', 					// Required if shipping is included.  Country code of shipping address.  2 char max.
						'shiptophonenum' => '',  				// Phone number for shipping address.  20 char max.
						'notetext' => '', 						// Note to the merchant.  255 char max.  
						'allowedpaymentmethod' => '', 			// The payment method type.  Specify the value InstantPaymentOnly.
						'allowpushfunding' => '', 				// Whether the merchant can accept push funding:  0 - Merchant can accept push funding.  1 - Merchant cannot accept push funding.  This will override the setting in the merchant's PayPal account.
						'paymentaction' => '', 					// How you want to obtain the payment.  When implementing parallel payments, this field is required and must be set to Order. 
						'paymentrequestid' => '',  				// A unique identifier of the specific payment request, which is required for parallel payments. 
						'sellerid' => '', 						// The unique non-changing identifier for the seller at the marketplace site.  This ID is not displayed.
						'sellerusername' => '', 				// The current name of the seller or business at the marketplace site.  This name may be shown to the buyer.
						'sellerpaypalaccountid' => ''			// A unique identifier for the merchant.  For parallel payments, this field is required and must contain the Payer ID or the email address of the merchant.
						);
		
		// For order items you populate a nested array with multiple $Item arrays.  
		// Normally you'll be looping through cart items to populate the $Item array
		// Then push it into the $OrderItems array at the end of each loop for an entire 
		// collection of all items in $OrderItems.
				
		$PaymentOrderItems = array();
		foreach($order_items as $order_item)
		{
			$Item = array(
						'name' => $order_item['item_name'], 								// Item name. 127 char max.
						'desc' => $order_item['item_description'], 								// Item description. 127 char max.
						'amt' => $order_item['item_price'], 								// Cost of item.
						'number' => '', 							// Item number.  127 char max.
						'qty' => $order_item['item_unit'], 								// Item qty on order.  Any positive integer.
						'taxamt' => '', 							// Item sales tax
						'itemurl' => '', 							// URL for the item.
						'itemweightvalue' => '', 					// The weight value of the item.
						'itemweightunit' => '', 					// The weight unit of the item.
						'itemheightvalue' => '', 					// The height value of the item.
						'itemheightunit' => '', 					// The height unit of the item.
						'itemwidthvalue' => '', 					// The width value of the item.
						'itemwidthunit' => '', 						// The width unit of the item.
						'itemlengthvalue' => '', 					// The length value of the item.
						'itemlengthunit' => '',  					// The length unit of the item.
						'itemcategory' => '', 						// Must be one of the following values:  Digital, Physical
						'ebayitemnumber' => '', 					// Auction item number.  
						'ebayitemauctiontxnid' => '', 				// Auction transaction ID number.  
						'ebayitemorderid' => '',  					// Auction order ID number.
						'ebayitemcartid' => ''						// The unique identifier provided by eBay for this order from the buyer. These parameters must be ordered sequentially beginning with 0 (for example L_EBAYITEMCARTID0, L_EBAYITEMCARTID1). Character length: 255 single-byte characters
						);
			array_push($PaymentOrderItems, $Item);
		}
		
		// Now we've got our OrderItems for this individual payment, 
		// so we'll load them into the $Payment array
		$Payment['order_items'] = $PaymentOrderItems;
		
		// Now we add the current $Payment array into the $Payments array collection
		array_push($Payments, $Payment);
		
		$BuyerDetails = array(
								'buyerid' => '', 				// The unique identifier provided by eBay for this buyer.  The value may or may not be the same as the username.  In the case of eBay, it is different.  Char max 255.
								'buyerusername' => '', 			// The username of the marketplace site.
								'buyerregistrationdate' => ''	// The registration of the buyer with the marketplace.
								);
								
		// For shipping options we create an array of all shipping choices similar to how order items works.
		$ShippingOptions = array();
		$Option = array(
						'l_shippingoptionisdefault' => '', 				// Shipping option.  Required if specifying the Callback URL.  true or false.  Must be only 1 default!
						'l_shippingoptionname' => '', 					// Shipping option name.  Required if specifying the Callback URL.  50 character max.
						'l_shippingoptionlabel' => '', 					// Shipping option label.  Required if specifying the Callback URL.  50 character max.
						'l_shippingoptionamount' => '' 					// Shipping option amount.  Required if specifying the Callback URL.  
						);
		array_push($ShippingOptions, $Option);
			
		// For billing agreements we create an array similar to working with 
		// payments, order items, and shipping options.	
		$BillingAgreements = array();
		$Item = array(
					  'l_billingtype' => 'RecurringPayments', 							// Required.  Type of billing agreement.  For recurring payments it must be RecurringPayments.  You can specify up to ten billing agreements.  For reference transactions, this field must be either:  MerchantInitiatedBilling, or MerchantInitiatedBillingSingleSource
					  'l_billingagreementdescription' => $order_items[0]['item_description'], 			// Required for recurring payments.  Description of goods or services associated with the billing agreement.  
					  'l_paymenttype' => '', 							// Specifies the type of PayPal payment you require for the billing agreement.  Any or IntantOnly
					  'l_billingagreementcustom' => ''					// Custom annotation field for your own use.  256 char max.
					  );
		array_push($BillingAgreements, $Item);
		
		$PayPalRequestData = array(
						'SECFields' => $SECFields, 
						'SurveyChoices' => $SurveyChoices, 
						'Payments' => $Payments, 
						'BuyerDetails' => $BuyerDetails, 
						'ShippingOptions' => $ShippingOptions, 
						'BillingAgreements' => $BillingAgreements
					);
					
		$PayPalResult = $this->paypal_pro->SetExpressCheckout($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
			//$errors = array('Errors'=>$PayPalResult['ERRORS']);
			//$this->load->view('payment/paypal_error',$errors);
			return $PayPalResult;
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
			return $PayPalResult;
		}
	}

	/**
	 * Get express checkout details
	 * @author Weerapat P.
	 * @param $data
	 * Called by payment_summary_paypal()
	 */
	function _get_express_checkout_details($data)
	{	
		$PayPalResult = $this->paypal_pro->GetExpressCheckoutDetails($data['token']);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
			//$errors = array('Errors'=>$PayPalResult['ERRORS']);
			//$this->load->view('payment/paypal_error',$errors);
			return $PayPalResult;
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
			return $PayPalResult;
		}
	}
	
	/**
	 * Do express checkout payment
	 * @author Weerapat P.
	 * @param $data
	 * Called by payment_summary_paypal()
	 */
	function _do_express_checkout_payment($data)
	{
		$user = $data['user'];
		$order = $data['order'];
		$order_items = $data['order_items'];
		
		$DECPFields = array(
							'token' => $data['token'], 								// Required.  A timestamped token, the value of which was returned by a previous SetExpressCheckout call.
							'payerid' => $data['payerid'], 							// Required.  Unique PayPal customer id of the payer.  Returned by GetExpressCheckoutDetails, or if you used SKIPDETAILS it's returned in the URL back to your RETURNURL.
							'returnfmfdetails' => '', 					// Flag to indiciate whether you want the results returned by Fraud Management Filters or not.  1 or 0.
							'giftmessage' => '', 						// The gift message entered by the buyer on the PayPal Review page.  150 char max.
							'giftreceiptenable' => '', 					// Pass true if a gift receipt was selected by the buyer on the PayPal Review page. Otherwise pass false.
							'giftwrapname' => '', 						// The gift wrap name only if the gift option on the PayPal Review page was selected by the buyer.
							'giftwrapamount' => '', 					// The amount only if the gift option on the PayPal Review page was selected by the buyer.
							'buyermarketingemail' => '', 				// The buyer email address opted in by the buyer on the PayPal Review page.
							'surveyquestion' => '', 					// The survey question on the PayPal Review page.  50 char max.
							'surveychoiceselected' => '',  				// The survey response selected by the buyer on the PayPal Review page.  15 char max.
							'allowedpaymentmethod' => '' 				// The payment method type. Specify the value InstantPaymentOnly.
						);
						
		// You can now utlize parallel payments (split payments) within Express Checkout.
		// Here we'll gather all the payment data for each payment included in this checkout 
		// and pass them into a $Payments array.  
		
		// Keep in mind that each payment will ahve its own set of OrderItems
		// so don't get confused along the way.	
							
		$Payments = array();
		$Payment = array(
						'amt' => $order['order_net_price'], 							// Required.  The total cost of the transaction to the customer.  If shipping cost and tax charges are known, include them in this value.  If not, this value should be the current sub-total of the order.
						'currencycode' => 'USD', 					// A three-character currency code.  Default is USD.
						'itemamt' => '', 						// Required if you specify itemized L_AMT fields. Sum of cost of all items in this order.  
						'shippingamt' => '', 					// Total shipping costs for this order.  If you specify SHIPPINGAMT you mut also specify a value for ITEMAMT.
						'shipdiscamt' => '', 					// Shipping discount for this order, specified as a negative number.
						'insuranceoptionoffered' => '', 		// If true, the insurance drop-down on the PayPal review page displays the string 'Yes' and the insurance amount.  If true, the total shipping insurance for this order must be a positive number.
						'handlingamt' => '', 					// Total handling costs for this order.  If you specify HANDLINGAMT you mut also specify a value for ITEMAMT.
						'taxamt' => '', 						// Required if you specify itemized L_TAXAMT fields.  Sum of all tax items in this order. 
						'desc' => '', 							// Description of items on the order.  127 char max.
						'custom' => '', 						// Free-form field for your own use.  256 char max.
						'invnum' => '', 						// Your own invoice or tracking number.  127 char max.
						'notifyurl' => base_url().'payment/paypal_listener', 						// URL for receiving Instant Payment Notifications
						'shiptoname' => '', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
						'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
						'shiptostreet2' => '', 					// Second street address.  100 char max.
						'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
						'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
						'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
						'shiptocountrycode' => '', 				// Required if shipping is included.  Country code of shipping address.  2 char max.
						'shiptophonenum' => '',  				// Phone number for shipping address.  20 char max.
						'notetext' => '', 						// Note to the merchant.  255 char max.  
						'allowedpaymentmethod' => '', 			// The payment method type.  Specify the value InstantPaymentOnly.
						'paymentaction' => '', 					// How you want to obtain the payment.  When implementing parallel payments, this field is required and must be set to Order. 
						'paymentrequestid' => '',  				// A unique identifier of the specific payment request, which is required for parallel payments. 
						'sellerid' => '', 						// The unique non-changing identifier for the seller at the marketplace site.  This ID is not displayed.
						'sellerusername' => '', 				// The current name of the seller or business at the marketplace site.  This name be shown to the buyer.
						'sellerregistrationdate' => '', 		// Date when the seller registered with the marketplace.
						'softdescriptor' => '', 				// A per transaction description of the payment that is passed to the buyer's credit card statement.
						'transactionid' => ''					// Tranaction identification number of the tranasction that was created.  NOTE:  This field is only returned after a successful transaction for DoExpressCheckout has occurred. 
						);
			
		// For order items you populate a nested array with multiple $Item arrays.  
		// Normally you'll be looping through cart items to populate the $Item array
		// Then push it into the $OrderItems array at the end of each loop for an entire 
		// collection of all items in $OrderItems.
					
		$PaymentOrderItems = array();
		foreach($order_items as $order_item)
		{
			$Item = array(
					'name' => '', 								// Item name. 127 char max.
					'desc' => '', 								// Item description. 127 char max.
					'amt' => '', 								// Cost of item.
					'number' => '', 							// Item number.  127 char max.
					'qty' => '', 								// Item qty on order.  Any positive integer.
					'taxamt' => '', 							// Item sales tax
					'itemurl' => '', 							// URL for the item.
					'itemweightvalue' => '', 					// The weight value of the item.
					'itemweightunit' => '', 					// The weight unit of the item.
					'itemheightvalue' => '', 					// The height value of the item.
					'itemheightunit' => '', 					// The height unit of the item.
					'itemwidthvalue' => '', 					// The width value of the item.
					'itemwidthunit' => '', 						// The width unit of the item.
					'itemlengthvalue' => '', 					// The length value of the item.
					'itemlengthunit' => '',  					// The length unit of the item.
					'itemurl' => '', 							// The URL for the item.
					'itemcategory' => '', 						// Must be one of the following:  Digital, Physical
					'ebayitemnumber' => '', 					// Auction item number.  
					'ebayitemauctiontxnid' => '', 				// Auction transaction ID number.  
					'ebayitemorderid' => '',  					// Auction order ID number.
					'ebayitemcartid' => ''						// The unique identifier provided by eBay for this order from the buyer. These parameters must be ordered sequentially beginning with 0 (for example L_EBAYITEMCARTID0, L_EBAYITEMCARTID1). Character length: 255 single-byte characters
					);
			array_push($PaymentOrderItems, $Item);
		}
		
		// Now we've got our OrderItems for this individual payment, 
		// so we'll load them into the $Payment array
		$Payment['order_items'] = $PaymentOrderItems;
		
		// Now we add the current $Payment array into the $Payments array collection
		array_push($Payments, $Payment);
		
		$UserSelectedOptions = array(
									 'shippingcalculationmode' => '', 	// Describes how the options that were presented to the user were determined.  values are:  API - Callback   or   API - Flatrate.
									 'insuranceoptionselected' => '', 	// The Yes/No option that you chose for insurance.
									 'shippingoptionisdefault' => '', 	// Is true if the buyer chose the default shipping option.  
									 'shippingoptionamount' => '', 		// The shipping amount that was chosen by the buyer.
									 'shippingoptionname' => '', 		// Is true if the buyer chose the default shipping option...??  Maybe this is supposed to show the name..??
									 );
									 
		$PayPalRequestData = array(
							'DECPFields' => $DECPFields, 
							'Payments' => $Payments, 
							'UserSelectedOptions' => $UserSelectedOptions
						);
						
		$PayPalResult = $this->paypal_pro->DoExpressCheckoutPayment($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
			//$errors = array('Errors'=>$PayPalResult['ERRORS']);
			//$this->load->view('payment/paypal_error',$errors);
			return $PayPalResult;
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
			return $PayPalResult;
		}
	}
	
	function _create_recurring_payments_profile($data)
	{		
		$user = $data['user'];
		$order = $data['order'];
		$order_items = $data['order_items'];
		
		$schedule_details = $data['schedule_details'];
		$billing_period = $data['billing_period'];
		
		$CRPPFields = array(
					'token' => $data['token'], 								// Token returned from PayPal SetExpressCheckout.  Can also use token returned from SetCustomerBillingAgreement.
						);
						
		$ProfileDetails = array(
							'subscribername' => $user['user_first_name'].' '.$user['user_last_name'], 	// Full name of the person receiving the product or service paid for by the recurring payment.  32 char max.
							'profilestartdate' => date('Y-m-d H:i:s'), 	// Required.  The date when the billing for this profiile begins.  Must be a valid date in UTC/GMT format.
							'profilereference' => $order['order_id'] 	// The merchant's own unique invoice number or reference ID.  127 char max.
						);
						
		$ScheduleDetails = array(
							'desc' => $schedule_details['desc'], 							// Required.  Description of the recurring payment.  This field must match the corresponding billing agreement description included in SetExpressCheckout.
							'maxfailedpayments' => $schedule_details['maxfailedpayments'], 	// The number of scheduled payment periods that can fail before the profile is automatically suspended.  
							'autobillamt' => $schedule_details['autobillamt'] 				// This field indiciates whether you would like PayPal to automatically bill the outstanding balance amount in the next billing cycle.  Values can be: NoAutoBill or AddToNextBilling
						);
						
		$BillingPeriod = array(
							'trialbillingperiod' => '', 
							'trialbillingfrequency' => '', 
							'trialtotalbillingcycles' => '', 
							'trialamt' => '', 
							'billingperiod' => $billing_period['billingperiod'], 			// Required.  Unit for billing during this subscription period.  One of the following: Day, Week, SemiMonth, Month, Year
							'billingfrequency' => $billing_period['billingfrequency'], 		// Required.  Number of billing periods that make up one billing cycle.  The combination of billing freq. and billing period must be less than or equal to one year. 
							'totalbillingcycles' => $billing_period['totalbillingcycles'],	// the number of billing cycles for the payment period (regular or trial).  For trial period it must be greater than 0.  For regular payments 0 means indefinite...until canceled.  
							'amt' => $billing_period['amt'], 								// Required.  Billing amount for each billing cycle during the payment period.  This does not include shipping and tax. 
							'currencycode' => $billing_period['currencycode'], 				// Required.  Three-letter currency code.
							'shippingamt' => '', 											// Shipping amount for each billing cycle during the payment period.
							'taxamt' => '' 													// Tax amount for each billing cycle during the payment period.
						);
						
		$ActivationDetails = array(
							'initamt' => '', 							// Initial non-recurring payment amount due immediatly upon profile creation.  Use an initial amount for enrolment or set-up fees.
							'failedinitamtaction' => '', 				// By default, PayPal will suspend the pending profile in the event that the initial payment fails.  You can override this.  Values are: ContinueOnFailure or CancelOnFailure
						);
						
		$CCDetails = array(
							'creditcardtype' => '', 	// Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
							'acct' => '', 						// Required.  Credit card number.  No spaces or punctuation.  
							'expdate' => '', 				// Required.  Credit card expiration date.  Format is MMYYYY
							'cvv2' => '', 						// Requirements determined by your PayPal account settings.  Security digits for credit card.
							'startdate' => '', 									// Month and year that Maestro or Solo card was issued.  MMYYYY
							'issuenumber' => ''									// Issue number of Maestro or Solo card.  Two numeric digits max.
						);
						
		$PayerInfo = array(
							'email' => $user['user_email'], 			// Email address of payer.
							'payerid' => '', 							// Unique PayPal customer ID for payer.
							'payerstatus' => '', 						// Status of payer.  Values are verified or unverified
							'business' => '' 							// Payer's business name.
						);
						
		$PayerName = array(
							'salutation' => '', 						// Payer's salutation.  20 char max.
							'firstname' => '', 							// Payer's first name.  25 char max.
							'middlename' => '', 						// Payer's middle name.  25 char max.
							'lastname' => '', 							// Payer's last name.  25 char max.
							'suffix' => ''								// Payer's suffix.  12 char max.
						);
						
		$BillingAddress = array(
								'street' => '', 			// Required.  First street address.
								'street2' => '', 			// Second street address.
								'city' => '', 				// Required.  Name of City.
								'state' => '', 				// Required. Name of State or Province.
								'countrycode' => '', 		// Required.  Country code.
								'zip' => '', 				// Required.  Postal code of payer.
								'phonenum' => '' 			// Phone Number of payer.  20 char max.
							);
							
		$ShippingAddress = array(
								'shiptoname' => '', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
								'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
								'shiptostreet2' => '', 					// Second street address.  100 char max.
								'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
								'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
								'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
								'shiptocountry' => '', 					// Required if shipping is included.  Country code of shipping address.  2 char max.
								'shiptophonenum' => ''					// Phone number for shipping address.  20 char max.
								);
								
		$PayPalRequestData = array(
							'CRPPFields' => $CRPPFields, 
							'ProfileDetails' => $ProfileDetails, 
							'ScheduleDetails' => $ScheduleDetails, 
							'BillingPeriod' => $BillingPeriod, 
							'ActivationDetails' => $ActivationDetails, 
							'CCDetails' => $CCDetails, 
							'PayerInfo' => $PayerInfo, 
							'PayerName' => $PayerName, 
							'BillingAddress' => $BillingAddress, 
							'ShippingAddress' => $ShippingAddress
						);	
						
		$PayPalResult = $this->paypal_pro->CreateRecurringPaymentsProfile($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
			//$errors = array('Errors'=>$PayPalResult['ERRORS']);
			//$this->load->view('payment/paypal_error',$errors);
			return $PayPalResult;
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
			return $PayPalResult;
		}	
	}
	
	function _manage_recurring_payments_profile_status($data)
	{
		$MRPPSFields = array(
						'profileid' => $data['profileid'], 	// Required. Recurring payments profile ID returned from CreateRecurring...
						'action' => $data['action'], 	// Required. The action to be performed.  Mest be: Cancel, Suspend, Reactivate
						'note' => $data['note']		// The reason for the change in status.  For express checkout the message will be included in email to buyers.  Can also be seen in both accounts in the status history.
						);
						
		$PayPalRequestData = array('MRPPSFields' => $MRPPSFields);
		
		$PayPalResult = $this->paypal_pro->ManageRecurringPaymentsProfileStatus($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
			//$errors = array('Errors'=>$PayPalResult['ERRORS']);
			//$this->load->view('payment/paypal_error',$errors);
			return $PayPalResult;
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
			return $PayPalResult;
		}		
	}
	
	function _get_recurring_payments_profile_details($profileid)
	{
		$GRPPDFields = array(
					   'profileid' => $profileid	// Profile ID of the profile you want to get details for.
					   );
					   
		$PayPalRequestData = array('GRPPDFields' => $GRPPDFields);
		
		$PayPalResult = $this->paypal_pro->GetRecurringPaymentsProfileDetails($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			log_message('error','paypal result error : '.print_r($PayPalResult['ERRORS']));
			//$errors = array('Errors'=>$PayPalResult['ERRORS']);
			//$this->load->view('paypal_error',$errors);
			return $PayPalResult;
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
			return $PayPalResult;
		}	
	}

}

/* End of file payment.php */
/* Location: ./application/controllers/payment.php */