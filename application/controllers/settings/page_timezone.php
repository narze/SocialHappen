<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_timezone extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this -> socialhappen -> check_logged_in();
	}
	
	function index($page_id = NULL){
		$this->load->library('settings');
		$config_name = 'timezone';
		$this->settings->view_page_app_settings($page_id, $config_name);
	}

	function view($page_id = NULL){

		if(!$this->socialhappen->check_admin(array('page_id' => $page_id),array('role_page_edit','role_all_company_pages_edit'))){
			//no access
		} else {
			$this->load->library('form_validation');
			$this->load->model('page_model','page');
			$page = $this->page->get_page_profile_by_page_id($page_id);
			$timezone = $this->page->get_page_timezone_by_page_id($page_id);
			
			$timezones = array(
				'' => 'Select Timezone',
				'-12' => '(UTC -12:00) Eniwetok, Kwajalein',
	            '-11' => '(UTC -11:00) Midway Island, Samoa',
	            '-10' => '(UTC -10:00) Hawaii',
	            '-9' => '(UTC -9:00) Alaska',
	            '-8' => '(UTC -8:00) Pacific Time (US &amp; Canada)',
	            '-7' => '(UTC -7:00) Mountain Time (US &amp; Canada)',
	            '-6' => '(UTC -6:00) Central Time (US &amp; Canada), Mexico City',
	            '-5' => '(UTC -5:00) Eastern Time (US &amp; Canada), Bogota, Lima',
	            '-4' => '(UTC -4:00) Atlantic Time (Canada), Caracas, La Paz',
	            '-3.5' => '(UTC -3:30) Newfoundland',
	            '-3' => '(UTC -3:00) Brazil, Buenos Aires, Georgetown',
	            '-2' => '(UTC -2:00) Mid-Atlantic',
	            '-1' => '(UTC -1:00 hour) Azores, Cape Verde Islands',
	            '0' => '(UTC) Western Europe Time, London, Lisbon, Casablanca',
	            '1' => '(UTC +1:00 hour) Brussels, Copenhagen, Madrid, Paris',
	            '2' => '(UTC +2:00) Kaliningrad, South Africa',
	            '3' => '(UTC +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
	            '3.5' => '(UTC +3:30) Tehran',
	            '4' => '(UTC +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
	            '4.5' => '(UTC +4:30) Kabul',
	            '5' => '(UTC +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
	            '5.5' => '(UTC +5:30) Bombay, Calcutta, Madras, New Delhi',
	            '5.75' => '(UTC +5:45) Kathmandu',
	            '6' => '(UTC +6:00) Almaty, Dhaka, Colombo',
	            '7' => '(UTC +7:00) Bangkok, Hanoi, Jakarta',
	            '8' => '(UTC +8:00) Beijing, Perth, Singapore, Hong Kong',
	            '9' => '(UTC +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
	            '9.5' => '(UTC +9:30) Adelaide, Darwin',
	            '10' => '(UTC +10:00) Eastern Australia, Guam, Vladivostok',
	            '11' => '(UTC +11:00) Magadan, Solomon Islands, New Caledonia',
	            '12' => '(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka'
				);

			$this->load->vars(array(
				'page_id' => $page_id,
				'timezones' => $timezones,
				'timezone' => $timezone,
				'updated' => FALSE
			));
			$this->form_validation->set_rules('timezone_list', 'Timezone', 'required|trim|is_numeric');
			
			$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
		
			if ($this->form_validation->run() == FALSE){
				
			} else {
				$timezone_offset_minute = set_value('timezone_list') * 60;
				if($this->page->update_page_timezone_by_page_id($page_id, $timezone_offset_minute)){
					$this->load->vars(array(
						'timezone' => $timezone_offset_minute,
						'updated' => TRUE
					));
				}
			}
			$this->load->view('settings/page_apps/timezone');
		}
	}
}
/* End of file page_timezone.php */
/* Location: ./application/controllers/settings/page_timezone.php */