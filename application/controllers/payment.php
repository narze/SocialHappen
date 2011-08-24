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
	function index()
	{
		// Load PayPal library
		$this->config->load('paypal_pro');
		
		$config = array(
			'APIUsername' => $this->config->item('APIUsername'), 	// PayPal API username of the API caller
			'APIPassword' => $this->config->item('APIPassword'), 	// PayPal API password of the API caller
			'APISignature' => $this->config->item('APISignature'), 	// PayPal API signature of the API caller
			'APISubject' => '', 									// PayPal API subject (email address of 3rd party user that has granted API permission for your app)
			'APIVersion' => $this->config->item('APIVersion')		// API version you'd like to use for your call.  You can set a default version in the class and leave this blank if you want.
		);
		
		$this->load->library('Paypal_pro', $config);	
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
			$this->load->view('payment/payment_form', 
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
			
			$data = array(
				'user' => $user,
				'package' => $package,
				'order' = array(
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
				),
				'order_item' = array(
					'order_id' => $order_id,
					'item_id' => $package['package_id'],
					'item_type' => $this->socialhappen->get_k('item_type', 'Package'),
					'item_name' => $package['package_name'],
					'item_description' => $package['package_detail'],
					'item_price' => $package['package_price'],
					'item_unit' => $item_unit,
					'item_discount' => $item_discount
				)
			);
			$payment_method = set_value('payment_method');
			switch($payment_method)
			{
				case 'bank_transfer': $this->_bank_transfer($data); break;
				case 'paypal': $this->_Set_express_checkout($data); break;
				case 'credit_card':
				case 'counter_service':
				default : break;
			}
		}
	}
	
	function payment_comfirm()
	{
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
			$this->load->view('payment/payment_form', 
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
	
	function _Set_express_checkout($data)
	{
		$user = $data['user'];
		$package = $data['package'];
		$order = $data['order'];
		$order_item = $data['order_item'];
		
		$SECFields = array(
							'token' => '', 								// A timestamped token, the value of which was returned by a previous SetExpressCheckout call.
							'maxamt' => '', 						// The expected maximum total amount the order will be, including S&H and sales tax.
							'returnurl' => '', 							// Required.  URL to which the customer will be returned after returning from PayPal.  2048 char max.
							'cancelurl' => '', 							// Required.  URL to which the customer will be returned if they cancel payment on PayPal's site.
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
						'amt' => '', 							// Required.  The total cost of the transaction to the customer.  If shipping cost and tax charges are known, include them in this value.  If not, this value should be the current sub-total of the order.
						'currencycode' => '', 					// A three-character currency code.  Default is USD.
						'itemamt' => '', 						// Required if you specify itemized L_AMT fields. Sum of cost of all items in this order.  
						'shippingamt' => '', 					// Total shipping costs for this order.  If you specify SHIPPINGAMT you mut also specify a value for ITEMAMT.
						'shipdiscamt' => '', 				// Shipping discount for this order, specified as a negative number.
						'insuranceoptionoffered' => '', 		// If true, the insurance drop-down on the PayPal review page displays the string 'Yes' and the insurance amount.  If true, the total shipping insurance for this order must be a positive number.
						'handlingamt' => '', 					// Total handling costs for this order.  If you specify HANDLINGAMT you mut also specify a value for ITEMAMT.
						'taxamt' => '', 						// Required if you specify itemized L_TAXAMT fields.  Sum of all tax items in this order. 
						'desc' => '', 							// Description of items on the order.  127 char max.
						'custom' => '', 						// Free-form field for your own use.  256 char max.
						'invnum' => '', 						// Your own invoice or tracking number.  127 char max.
						'notifyurl' => '', 						// URL for receiving Instant Payment Notifications
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
					'itemurl' => '', 							// URL for the item.
					'itemcategory' => '', 						// Must be one of the following values:  Digital, Physical
					'ebayitemnumber' => '', 					// Auction item number.  
					'ebayitemauctiontxnid' => '', 				// Auction transaction ID number.  
					'ebayitemorderid' => '',  					// Auction order ID number.
					'ebayitemcartid' => ''						// The unique identifier provided by eBay for this order from the buyer. These parameters must be ordered sequentially beginning with 0 (for example L_EBAYITEMCARTID0, L_EBAYITEMCARTID1). Character length: 255 single-byte characters
					);
		array_push($PaymentOrderItems, $Item);
		
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
					  'l_billingtype' => '', 							// Required.  Type of billing agreement.  For recurring payments it must be RecurringPayments.  You can specify up to ten billing agreements.  For reference transactions, this field must be either:  MerchantInitiatedBilling, or MerchantInitiatedBillingSingleSource
					  'l_billingagreementdescription' => '', 			// Required for recurring payments.  Description of goods or services associated with the billing agreement.  
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
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
}

/* End of file payment.php */
/* Location: ./application/controllers/payment.php */