<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_user_password extends CI_Migration {

	public function up(){
		$fields = array(
			'user_is_player' => array(
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0
			),
			'user_phone' => array(
				'type' => 'VARCHAR',
				'constraint' => 20,
				'null' => TRUE
			),
			'user_password' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE)
		);
		$this->dbforge->add_column('user', $fields);
		$modify_fields = array(
			'user_facebook_id' => array(
				'type' => 'BIGINT',
				'constraint' => 20,
				'unsigned' => TRUE,
				'null' => TRUE
			)
		);
		$this->dbforge->modify_column('user', $modify_fields);
		echo 'Upgraded to version 3<br />';
	}

	public function down()
	{
		$this->dbforge->drop_column('user','user_phone');
		$this->dbforge->drop_column('user','user_password');
		echo 'Downgraded to version 2<br />';
	}
}