<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_First_version extends CI_Migration {
	public $tables = array(
							'app',
							//'app_statistic',
							'campaign',
							'company',
							'company_apps',
							//'config_item',
							//'config_item_template',
							//'cron_job',
							'installed_apps',
							'page',
							'user',
							'user_apps',
							'user_campaigns',
							'user_companies',
							'user_role',
							'user_pages',
							'package',
							'package_users',
							'package_apps',
							'order',
							'order_items',
							'page_user_data'
						);
	public function up()
	{
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
							    'app_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'app_name' => field_option('VARCHAR', 50, $default, $null, $autoinc, $unsigned),
							    'app_type_id' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'app_maintainance' => field_option('INT', 1, 0, $null, $autoinc, $unsigned),
							    'app_show_in_list' => field_option('INT', 1, 1, $null, $autoinc, $unsigned),
							    'app_description' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
							    'app_secret_key' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_install_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_install_page_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'app_config_url' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
								'app_config_facebook_canvas_path' => field_option('TEXT', $constraint, NULL, TRUE, $autoinc, $unsigned),
							    'app_support_page_tab' => field_option('INT', 1, $default, $null, $autoinc, $unsigned),
							    'app_icon' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'app_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'app_facebook_api_key' => field_option('VARCHAR', 32, $default, $null, $autoinc, $unsigned)
							),
							/*'app_statistic' => array(
							    'app_install_id' => field_option('BIGINT', 20, $default, $null, $autoinc, $unsigned),
							    'job_time' => field_option('TIMESTAMP', $constraint, 'CURRENT_TIMESTAMP', $null, $autoinc, $unsigned),
							    'job_id' => field_option('BIGINT', 20, $default, $null, $autoinc, $unsigned),
							    'active_user' => field_option('BIGINT', 20, $default, $null, $autoinc, $unsigned),
							),*/
							'campaign' => array(
							    'campaign_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'app_install_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'campaign_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'campaign_detail' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'campaign_status_id' => field_option('INT', 2, $default, $null, $autoinc, TRUE),
							    'campaign_start_timestamp' => field_option('TIMESTAMP', $constraint, $default , $null, $autoinc, $unsigned),
							    'campaign_end_timestamp' => field_option('TIMESTAMP', $constraint, $default , $null, $autoinc, $unsigned),
								'campaign_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'campaign_end_message' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned)
							),
							'company' => array(
							    'company_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'creator_user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'company_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_detail' => field_option('VARCHAR', 255, $default, TRUE, $autoinc, $unsigned),
							    'company_address' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'company_email' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_telephone' => field_option('VARCHAR', 20, $default, $null, $autoinc, $unsigned),
							    'company_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'company_username' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_password' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'company_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'company_website' => field_option('VARCHAR', 255, $default, TRUE, $autoinc, $unsigned)
							),
							'company_apps' => array(
							    'company_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'app_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'available_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							),
							'installed_apps' => array(
							    'app_install_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'company_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'app_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'app_install_status_id' => field_option('INT', 1, $default, $null, $autoinc, TRUE),
							    'app_install_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'page_id' => field_option('BIGINT', 20, 0, TRUE, $autoinc, TRUE),
							    'app_install_secret_key' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
							    'order_in_dashboard' => field_option('INT', 5, 0, $null, $autoinc, TRUE),
							    'facebook_tab_url' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							),
							'page' => array(
							    'page_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'facebook_page_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'company_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'page_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'page_detail' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
							    'page_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'order_in_dashboard' => field_option('INT', 5, 0, $null, $autoinc, TRUE),
								'page_status_id' => field_option('INT', 1, 1, $null, $autoinc, TRUE),
								'page_app_installed_id' => field_option('BIGINT', 20, 0, $null, $autoinc, TRUE),
								'page_installed' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'page_user_fields' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
							    'facebook_tab_url' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'enable_facebook_page_tab' => field_option('BOOLEAN', $constraint, 1, $null, $autoinc, $unsigned),
							    'enable_facebook_tab_bar' => field_option('BOOLEAN', $constraint, 1, $null, $autoinc, $unsigned),
							    'enable_socialhappen_features' => field_option('BOOLEAN', $constraint, 1, $null, $autoinc, $unsigned),
							    'page_member_limit' => field_option('INT', 11, 0, $null, $autoinc, TRUE),
							),
							'user' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
							    'user_first_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_last_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_email' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_facebook_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'user_facebook_access_token' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'user_last_seen' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
								'user_gender_id' => field_option('INT', 1, 1, TRUE, $autoinc, TRUE),
								'user_birth_date' => field_option('DATE', $constraint, $default, TRUE, $autoinc, $unsigned),
								'user_about' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
								'user_twitter_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'user_twitter_access_token' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'user_twitter_access_token_secret' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
							    'user_timezone_offset' => field_option('INT', 10, 0, $null, $autoinc, $unsigned),
							    'user_is_developer' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned)
							),
							'user_apps' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'app_install_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'user_apps_register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
							    'user_apps_last_seen' => field_option('TIMESTAMP', $constraint, $default, $null, $autoinc, $unsigned),
							),
							'user_campaigns' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'campaign_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							),
							'user_companies' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'company_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'user_role' => field_option('INT', 2, 0, $null, $autoinc, TRUE),
							),
							'user_role' => array(
							    'user_role_id' => field_option('INT', 2, $default, $null, TRUE, TRUE),
							    'user_role_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'role_all' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_company_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_company_add' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_company_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_company_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_all_company_pages_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_pages_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_pages_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_all_company_apps_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_apps_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_apps_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_all_company_campaigns_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_campaigns_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_all_company_campaigns_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_page_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_page_add' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_page_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_page_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_app_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_app_add' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_app_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_app_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								//'role_campaign_view' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_campaign_add' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_campaign_edit' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'role_campaign_delete' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
							),
							'user_pages' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'page_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'user_role' => field_option('INT', 2, 0, $null, $autoinc, TRUE),
							),
							'package' => array(
								'package_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
								'package_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'package_detail' => field_option('VARCHAR', 255, $default, TRUE, $autoinc, $unsigned),
								'package_image' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'package_max_companies' => field_option('INT', 10, 0, $null, $autoinc, TRUE),
								'package_max_pages' => field_option('INT', 10, 0, $null, $autoinc, TRUE),
								'package_max_users' => field_option('INT', 10, 0, $null, $autoinc, TRUE),
								'package_price' => field_option('DOUBLE', $constraint, 0, $null, $autoinc, TRUE),
								'package_custom_badge' => field_option('BOOLEAN', $constraint, 0, $null, $autoinc, $unsigned),
								'package_duration' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned)
								
							),
							'package_users' => array(
								'package_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'package_expire TIMESTAMP NOT NULL DEFAULT "0000-00-00 00:00:00"',
							),
							'package_apps' => array(
								'package_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'app_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							),
							'order' => array(
								'order_id' => field_option('BIGINT', 20, $default, $null, TRUE, TRUE),
								'order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
								'order_status_id' => field_option('INT', 10, $default, $null, $autoinc, TRUE),
								'order_net_price' => field_option('DOUBLE', $constraint, 0, $null, $autoinc, TRUE),
								'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'payment_method' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'billing_info' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned)
							),
							'order_items' => array(
								'order_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'item_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
								'item_type_id' => field_option('INT', 10, $default, $null, $autoinc, TRUE),
								'item_name' => field_option('VARCHAR', 255, $default, $null, $autoinc, $unsigned),
								'item_description' => field_option('TEXT', $constraint, $default, TRUE, $autoinc, $unsigned),
								'item_price' => field_option('DOUBLE', $constraint, 0, $null, $autoinc, TRUE),
								'item_unit' => field_option('INT', 10, 1, $null, $autoinc, TRUE),
								'item_discount' => field_option('BIGINT', 20, 0, TRUE, $autoinc, TRUE)
							),
							'page_user_data' => array(
							    'user_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'page_id' => field_option('BIGINT', 20, $default, $null, $autoinc, TRUE),
							    'user_data' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned)
							)
						);
		$keys = array(
						'app' => array('app_id'),
						//'app_statistic' => array('app_install_id','job_time'),
						'campaign' => array('campaign_id'),
						'company' => array('company_id'),
						'company_apps' => array('company_id', 'app_id'),
						//'config_item' => array('app_install_id','config_key'),
						//'config_item_template' => array('app_id','config_key'),
						//'VARCHAR_job' => array('job_id'),
						'installed_apps' => array('app_install_id'),
						'page' => array('page_id'),
						'user' => array('user_id'),
						'user_apps' => array('user_id', 'app_install_id'),
						'user_campaigns' => array('user_id', 'campaign_id'),
						'user_companies' => array('user_id', 'company_id'),
						'user_role' => array('user_role_id'),
						'user_pages' => array('user_id', 'page_id'),
						'package' => array('package_id'),
						'package_users' => array('user_id'),
						'package_apps' => array('package_id','app_id'),
						'order' => array('order_id'),
						'order_items' => array('order_id','item_id', 'item_type_id'),
						'page_user_data' => array('user_id','page_id')
					);
		
		$tables = $this->tables;
		
		foreach ($tables as $table){
			$this->dbforge->add_field($fields[$table]);
			foreach ($keys[$table] as $primary_key){
				$this->dbforge->add_key($primary_key, TRUE);
			}
			if($this->dbforge->create_table($table, TRUE)){
				echo "Created table : {$table}<br />";	
			}
		}

		if ( ! $this->db->table_exists('sessions'))
		{
			$this->dbforge->add_field(array(
				'session_id' => field_option('VARCHAR', 40, '0', $null, $autoinc, $unsigned),
				'ip_address' => field_option('VARCHAR', 16, '0', $null, $autoinc, $unsigned),
				'user_agent' => field_option('VARCHAR', 120, $default, $null, $autoinc, $unsigned),
				'last_activity' => field_option('INT', 10, 0, $null, $autoinc, TRUE),
				'user_data' => field_option('TEXT', $constraint, $default, $null, $autoinc, $unsigned),
				'user_id' => field_option('BIGINT', 20, $default, TRUE, $autoinc, TRUE),
			
			));

			$this->dbforge->create_table('sessions', TRUE);
			$this->db->query("CREATE INDEX last_activity_idx ON ".$this->db->dbprefix('sessions')." (last_activity)");

		}
		$this->db->query("CREATE UNIQUE INDEX user_facebook_id ON ".$this->db->dbprefix('user')." (user_facebook_id)");
		
		echo 'Upgraded to 2<br />';
	}

	public function down()
	{
		$tables = array_map(array($this->db,'dbprefix'), $this->tables);
		
		foreach ($tables as $table){
			$table = str_replace($this->db->dbprefix,'',$table);
			
			if($this->dbforge->drop_table($table, TRUE)){
				echo "Dropped table : {$table}<br />";	
			}
		}
		echo 'Downgraded to 1<br />';
	}
}