<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_credits_action extends CI_Migration {

	public function up(){

    /** audit_actions **/
    // update audit action by audit_action_id
    $platform_audit_actions = array(
      array(
        'app_id' => 0,
        'action_id' => 205,
        'description' => 'Add Credits',
        'stat_app' => FALSE,
        'stat_page' => FALSE,
        'stat_campaign' => FALSE,
        'format_string' => '{user:user_id} add {string:object} credits to company {company:company_id} and this company got {string:objecti} credits total',
        'score' => 0
      )
    );


    $this->load->model('audit_action_model');
    $success_count = 0;
    foreach($platform_audit_actions as $new_audit_action) {
      $query = array(
        'app_id' => $new_audit_action['app_id'],
        'action_id' => $new_audit_action['action_id']
      );

      $update_data = array(
        '$set' => $new_audit_action
      );

      echo 'Updating audit_action ' . $new_audit_action['description'];
      if($this->audit_action_model->upsert($query, $update_data)) {
        $success_count++;
        echo ' Success!';
      } else {
        echo ' Failed!';
      }

      echo '<br />';
    }
    echo 'Updated '.$success_count.'/'.count($platform_audit_actions).' audit_actions<br />';
		echo 'Upgraded to version 12<br />';
	}

	public function down()
	{
		//Do nothing
		echo 'Downgraded to version 11<br />';
	}
}