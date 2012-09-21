<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Nullable_facebook_access_token extends CI_Migration {

	public function up(){
    $modify_fields = array(
      'user_facebook_access_token' => array(
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => TRUE
      )
    );
    $this->dbforge->modify_column('user', $modify_fields);
    echo 'Upgraded to version 8<br />';
	}

	public function down()
	{
		$modify_fields = array(
      'user_facebook_access_token' => array(
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => FALSE
      )
    );
    $this->dbforge->modify_column('user', $modify_fields);
		echo 'Downgraded to version 7<br />';
	}
}