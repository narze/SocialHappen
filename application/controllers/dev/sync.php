<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @class Sync
 * @category Controller
 */
class Sync extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		$this->output->enable_profiler(TRUE);
	}

	function index(){
		echo '<a href="'.base_url().'dev/sync/db_reset">RESET (drop -> create -> insert data)</a><br />';
		echo '<a href="'.base_url().'dev/sync/generate_field_code">Generate field PHP code</a><br />';
		echo '<a href="'.base_url().'dev/sync/create_database">Create datebase "socialhappen"</a>';
		
	}
	
	function db_reset(){
		$this->drop_tables();
		$this->create_tables();
		$this->special_cases_after_create();
		$this->insert_test_data();
		echo "Database reset successfully";
	}
	
	function generate_field_code(){
		$tables = $this->db->list_tables();
		foreach ($tables as $table){
		   $fields = $this->db->field_data($table);
			echo "'".str_replace($this->db->dbprefix,'',$table)."' => array(<br />";
			foreach ($fields as $field){	
 		 		 echo "&nbsp;&nbsp;&nbsp;&nbsp;'".$field->name."' => field_option('".str_replace('STRING','VARCHAR',strtoupper($field->type))."', \$constraint, \$default, \$null, \$autoinc, \$unsigned),<br />";
			}
			echo "),<br />";
		}
	}
	
	function drop_tables(){
		$tables = $this->db->list_tables();
		foreach ($tables as $table){
			$table = str_replace($this->db->dbprefix,'',$table);
		    if($this->dbforge->drop_table($table)){
		    	echo "Dropped table : {$table}<br />";	
		    }
		}
	}
	
	function create_database(){
		$this->dbforge->create_database('socialhappen');
	}
	
	function create_tables(){
		function field_option($type, $constraint, $default, $null, $auto_increment, $unsigned){
			$options = array();
			if(isset($type)){
				$options['type'] = $type;
			}
			if(isset($constraint)){
				$options['constraint'] = $constraint;
			}
			if(isset($default)){
				$options['default'] = $default;
			}
			if(isset($null)){
				$options['null'] = $null;
			}
			if(isset($auto_increment)){
				$options['auto_increment'] = $auto_increment;
			}
			if(isset($unsigned)){
				$options['unsigned'] = $unsigned;
			}
			return $options;
		}
		
		$constraint = NULL;
		$default = NULL;
		$null = FALSE;
		$autoinc = NULL;
		$unsigned = NULL;
		
		$fields = array(
							'app' => array(
							    'app_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'app_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_type_id' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'app_maintainance' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_show_in_list' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_description' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_secret_key' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_install_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_config_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_support_page_tab' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'app_install_status' => array(
							    'app_install_status_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'app_install_status_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_install_status_description' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							/*'app_statistic' => array(
							    'app_install_id' => field_option('INT', 20, $default, $null, $autoinc, $unsigned),
							    'job_time' => field_option('TIMESTAMP', $constraint, 'CURRENT_TIMESTAMP', $null, $autoinc, $unsigned),
							    'job_id' => field_option('INT', 20, $default, $null, $autoinc, $unsigned),
							    'active_user' => field_option('INT', 20, $default, $null, $autoinc, $unsigned),
							),*/
							'app_type' => array(
							    'app_type_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'app_type_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_type_description' => field_option('VARCHAR', 255, $default, TRUE, $autoinc, $unsigned),
							),
							'campaign' => array(
							    'campaign_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'app_install_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'campaign_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'campaign_detail' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'campaign_status_id' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'campaign_active_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'campaign_all_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'campaign_start_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'campaign_end_timestamp' => field_option('TIMESTAMP', $constraint, $default , $null, $autoinc, $unsigned),
							),
							'campaign_status' => array(
							    'campaign_status_id' => field_option('INT', 1, $default, $null, TRUE, TRUE),
							    'campaign_status_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'company' => array(
							    'company_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'creator_user_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'company_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_address' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'company_email' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_telephone' => field_option('VARCHAR', 20, $default, $null, $autoinc, $unsigned),
							    'company_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'company_username' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_password' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'company_apps' => array(
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'app_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'available_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							),
							'company_pages' => array(
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'page_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							),
							/*'config_item' => array(
							    'app_install_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'config_key' => field_option('VARCHAR', 64, $default, $null, $autoinc, $unsigned),
							    'config_value' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'config_item_template' => array(
							    'app_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'config_key' => field_option('64', $constraint, $default, $null, $autoinc, $unsigned),
							    'config_value' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'cron_job' => array(
							    'job_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'job_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'job_start' => field_option('TIMESTAMP', $constraint, 'CURRENT_TIMESTAMP', $null, $autoinc, $unsigned),
							    'job_finish' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
							    'job_status' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),*/
							'installed_apps' => array(
							    'app_install_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'app_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'app_install_status' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_install_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'page_id' => field_option('INT', 20, $default, TRUE, $autoinc, TRUE),
							    'app_install_secret_key' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'page' => array(
							    'page_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'facebook_page_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'page_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'page_detail' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'page_all_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'page_new_member' => field_option('INT', 11, $default, $null, $autoinc, TRUE),
							    'page_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'user' => array(
							    'user_id' => field_option('INT', 20, $default, $null, TRUE, TRUE),
							    'user_facebook_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'user_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'user_last_seen' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'user_apps' => array(
							    'user_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'app_install_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'user_apps_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'user_apps_last_seen' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'user_campaigns' => array(
							    'user_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'campaign_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							),
							'user_companies' => array(
							    'user_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'company_id' => field_option('INT', 20, $default, $null, $autoinc, TRUE),
							    'user_role' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							)
						);
		$keys = array(
						'app' => array('app_id'),
						'app_install_status' => array('app_install_status_id'),
						//'app_statistic' => array('app_install_id','job_time'),
						'app_type' => array('app_type_id'),
						'campaign' => array('campaign_id'),
						'campaign_status' => array('campaign_status_id'),
						'company' => array('company_id'),
						'company_apps' => array('company_id', 'app_id'),
						'company_pages' => array('company_id', 'page_id'),
						//'config_item' => array('app_install_id','config_key'),
						//'config_item_template' => array('app_id','config_key'),
						//'VARCHAR_job' => array('job_id'),
						'installed_apps' => array('app_install_id'),
						'page' => array('page_id'),
						'user' => array('user_id'),
						'user_apps' => array('user_id', 'app_install_id'),
						'user_campaigns' => array('user_id', 'campaign_id'),
						'user_companies' => array('user_id', 'company_id')
					);
		$tables = array(
							'app',
							'app_install_status',
							//'app_statistic',
							'app_type',
							'campaign',
							'campaign_status',
							'company',
							'company_apps',
							'company_pages',
							//'config_item',
							//'config_item_template',
							//'cron_job',
							'installed_apps',
							'page',
							'user',
							'user_apps',
							'user_campaigns',
							'user_companies'
						);
		$tables = array_map(array($this->db,'dbprefix'), $tables);
		
		foreach ($tables as $table){
			$table = str_replace($this->db->dbprefix,'',$table);
			$this->dbforge->add_field($fields[$table]);
			foreach ($keys[$table] as $primary_key){
				$this->dbforge->add_key($primary_key, TRUE);
			}
			if($this->dbforge->create_table($table, TRUE)){
				echo "Created table : {$table}<br />";	
			}
		}
	}

	function special_cases_after_create(){
		$this->db->query("CREATE UNIQUE INDEX user_facebook_id ON ".$this->db->dbprefix('user')." (user_facebook_id)");
	}
	
	function insert_test_data(){
		$app = array(
					array(
					    'app_id' => 1,
					    'app_name' => 'Feed',
					    'app_type_id' => 1,
					    'app_maintainance' => 0,
					    'app_show_in_list' => 1,
					    'app_description' => 'RSS Feed in facebook tab',
					    'app_secret_key' =>  '11111111111111111111111111111111',
					    'app_url' =>  'http://socialhappen.dyndns.org/feed?app_install_id={app_install_id}', 
					    'app_install_url' => 'http://socialhappen.dyndns.org/feed/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}',
					    'app_config_url' =>  'http://socialhappen.dyndns.org/feed/sh/config?app_install_id={app_install_id}&user_facebook_id={user_facebook_id}&app_install_secret_key={app_install_secret_key}',
					    'app_support_page_tab' =>  1,
					    'app_image' =>  ''
					),
					array(
					    'app_id' => 2, 
					    'app_name' => 'Facebook Register', 
					    'app_type_id' => 2, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Campaign register using Facebook id',
					    'app_secret_key' =>  '22222222222222222222222222222222', 
					    'app_url' => 'http://socialhappen.dyndns.org/fbreg?app_install_id={app_install_id}', 
					    'app_install_url' => 'http://socialhappen.dyndns.org/fbreg/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 
					    'app_config_url' => 'http://socialhappen.dyndns.org/fbreg/sh/config?app_install_id={app_install_id}&user_facebook_id={user_facebook_id}&app_install_secret_key={app_install_secret_key}', 
					    'app_support_page_tab' => 0, 
					    'app_image' => ''
					),
					array(
					    'app_id' => 3, 
					    'app_name' => 'Share to get it', 
					    'app_type_id' => 3, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Share links by twitter / facebook to get file url', 
					    'app_secret_key' => '33333333333333333333333333333333', 
					    'app_url' => 'http://socialhappen.dyndns.org/sharetogetit?app_install_id={app_install_id}', 
					    'app_install_url' => 'http://socialhappen.dyndns.org/sharetogetit/sh/install?company_id={company_id}&user_facebook_id={user_facebook_id}', 
					    'app_config_url' => 'http://socialhappen.dyndns.org/sharetogetit/sh/config/{app_install_id}/{user_facebook_id}/{app_install_secret_key}', 
					    'app_support_page_tab' => 0, 
					    'app_image' => ''
					),
					array(
					    'app_id' => 4, 
					    'app_name' => 'Facebook CMS', 
					    'app_type_id' => 1, 
					    'app_maintainance' => 0, 
					    'app_show_in_list' => 1, 
					    'app_description' => 'Content Management System on Facebook', 
					    'app_secret_key' => '44444444444444444444444444444444', 
					    'app_url' => 'http://socialhappen.dyndns.org/fbcms/blog/{app_install_id}/', 
					    'app_install_url' => 'http://socialhappen.dyndns.org/fbcms/platform/install/{company_id}/{user_facebook_id}/', 
					    'app_config_url' => 'http://socialhappen.dyndns.org/fbcms/platform/config/{app_install_id}/{user_facebook_id}/{app_install_secret_key}/', 
					    'app_support_page_tab' => 1, 
					    'app_image' => ''
					)
				);
		$this->db->insert_batch('app', $app);
		
		$app_install_status = array(
									array(
									    'app_install_status_id' => 1,
									    'app_install_status_name' => 'active',
									    'app_install_status_description' => 'Active'
									)
								);
		$this->db->insert_batch('app_install_status', $app_install_status);
		
		$app_type = array(
							array(
							    'app_type_id' => 1,
							    'app_type_name' => 'Page Only',
							    'app_type_description' => 'Apps will be in page only',
							),
							array(
							    'app_type_id' => 2,
							    'app_type_name' => 'Support Page',
							    'app_type_description' => 'Apps can be installed into page'
							),
							array(
							    'app_type_id' => 3, 
							    'app_type_name' => 'Standalone', 
							    'app_type_description' => 'Apps cannot be installed into page'
							)
					);
		$this->db->insert_batch('app_type', $app_type);
				
		$campaign = array(
							array(
							    'campaign_id' => 1,
							    'app_install_id' =>  1, 
							    'campaign_name' => 'Campaign test 1',
							    'campaign_detail' => 'Campaign test detail 1',
							    'campaign_status_id' => 1, 
							    'campaign_active_member' => 2,
							    'campaign_all_member' => 10, 
							    'campaign_start_timestamp' => '2011-05-19 18:29:43',
							    'campaign_end_timestamp' => '2012-05-18 00:00:00'
							),
							array(
							    'campaign_id' => 2, 
							    'app_install_id' => 2, 
							    'campaign_name' => 'Campaign test 2',
							    'campaign_detail' => 'Campaign test detail 2', 
							    'campaign_status_id' => 1, 
							    'campaign_active_member' => 3, 
							    'campaign_all_member' => 5, 
							    'campaign_start_timestamp' => '2011-05-18 18:05:46', 
							    'campaign_end_timestamp' => '2011-06-18 00:00:00'
							)
						);
		$this->db->insert_batch('campaign', $campaign);
		
		$campaign_status = array(
								array(
								    'campaign_status_id' => 1,
								    'campaign_status_name' => 'Inactive',
								),
								array(
								    'campaign_status_id' => 2,
								    'campaign_status_name' => 'Active',
								)
							);
		$this->db->insert_batch('campaign_status', $campaign_status);
		
		$company = array(
						array(
							    'company_id' => 1, 
							    'creator_user_id' => 0, 
							    'company_name' => 'Company test 1', 
							    'company_address' => '', 
							    'company_email' => 'test1@figabyte.com', 
							    'company_telephone' => '022485555', 
							    'company_register_date' => '2011-05-09 17:52:17', 
							    'company_username' => '', 
							    'company_password' => '',
							    'company_image' => ''
							)
						);
		$this->db->insert_batch('company', $company);
		
		$company_apps = array(
							array(
							    'company_id' => 1, 
							    'app_id' => 1, 
							    'available_date' => '2011-05-19 16:01:20'
							),
							array(
							    'company_id' => 1, 
							    'app_id' => 2, 
							    'available_date' => '2011-05-19 16:01:20'
							)
						);
		$this->db->insert_batch('company_apps', $company_apps);
		
		$company_pages = array(
							array(
							    'company_id' => 1,
							    'page_id' => 1
							)
						);
		$this->db->insert_batch('company_pages', $company_pages);
						
		$installed_apps = array(
								array(
								    'app_install_id' => 1, 
								    'company_id' => 1, 
								    'app_id' => 1, 
								    'app_install_status' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => '457f81902f7b768c398543e473c47465'
								),
								array(
								  	'app_install_id' => 2, 
								    'company_id' => 1, 
								    'app_id' => 2, 
								    'app_install_status' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => 'b4504b54bb0c27a22fedba10cca4eb55'
								),
								array(
								    'app_install_id' => 3, 
								    'company_id' => 1, 
								    'app_id' => 3, 
								    'app_install_status' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => '1dd5a598414f201bc521348927c265c3'
								),
								array(
								  	'app_install_id' => 4, 
								    'company_id' => 1, 
								    'app_id' => 4, 
								    'app_install_status' => 1, 
								    'app_install_date' => '2011-05-18 18:37:01', 
								    'page_id' => 1, 
								    'app_install_secret_key' => '19323810aedbbc8384b383fa21904626'
								)
							);
		$this->db->insert_batch('installed_apps', $installed_apps);
		
		$page = array(
					array(
						    'page_id' => 1, 
						    'facebook_page_id' => 4321, 
						    'company_id' => 1, 
						    'page_name' => 'Test name', 
						    'page_detail' => 'detail', 
						    'page_all_member' => 22, 
						    'page_new_member' => 222, 
						    'page_image' => ''
						)
					);
		$this->db->insert_batch('page', $page);
		
		$user = array(
					array(
					    'user_id' => 1, 
					    'user_facebook_id' => 713558190, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
						),
					array(
					    'user_id' => 2, 
					    'user_facebook_id' => 637741627, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 3, 
					    'user_facebook_id' => 631885465, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 4, 
					    'user_facebook_id' => 755758746, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					),
					array(
					    'user_id' => 5, 
					    'user_facebook_id' => 508840994, 
					    'user_register_date' => '2011-05-09 17:36:14',
					    'user_last_seen' => '2011-05-18 12:57:24'
					)
				);
		$this->db->insert_batch('user', $user);
		
		$user_apps = array(
							array(
							    'user_id' => 1, 
							    'app_install_id' => 1, 
							    'user_apps_register_date' => '2011-05-19 19:12:20', 
							    'user_apps_last_seen' => '0000-00-00 00:00:00'
							),
							array(
							    'user_id' => 2, 
							    'app_install_id' => 2, 
							    'user_apps_register_date' => '2011-05-19 19:12:20', 
							    'user_apps_last_seen' => '0000-00-00 00:00:00'
							)
						);
		$this->db->insert_batch('user_apps', $user_apps);
		
		$user_campaigns = array(
								array(
								    'user_id' => 1,
								    'campaign_id' => 1
								),
								array(
								    'user_id' => 2,
								    'campaign_id' => 2
								),
								array(
								    'user_id' => 3,
								    'campaign_id' => 1
								),
								array(
								    'user_id' => 4,
								    'campaign_id' => 2
								),
								array(
								    'user_id' => 5,
								    'campaign_id' => 2
								)
							);
		$this->db->insert_batch('user_campaigns', $user_campaigns);
								
		$user_companies = array(
								array(
								    'user_id' => 1,
								    'company_id' => 1,
								    'user_role' => 0
								),
								array(
								    'user_id' => 2,
								    'company_id' => 1,
								    'user_role' => 0
								),
								array(
								    'user_id' => 3,
								    'company_id' => 1,
								    'user_role' => 0
								),
								array(
								    'user_id' => 4,
								    'company_id' => 1,
								    'user_role' => 0
								),
								array(
								    'user_id' => 5,
								    'company_id' => 1,
								    'user_role' => 0
								)
							);
		$this->db->insert_batch('user_companies', $user_companies);
		echo "Test data added<br />";
	}
}
/* End of file sync.php */
/* Location: ./application/controllers/dev/sync.php */