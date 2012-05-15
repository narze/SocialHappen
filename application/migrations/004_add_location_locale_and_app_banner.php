<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_location_locale_and_app_banner extends CI_Migration {

	public function up(){
		$fields = array(
			'user_location' => array(
				'type' => 'VARCHAR',
				'constraint' => 50,
				'default' => NULL,
				'null' => TRUE
			),
			'user_locale' => array(
				'type' => 'VARCHAR',
				'constraint' => 20,
				'default' => 'en_US'
			)
		);
		$this->dbforge->add_column('user', $fields);

		$fields = array(
			'app_banner' => array(
				'type' => 'VARCHAR',
				'constraint' => 255
			)
		);
		$this->dbforge->add_column('app', $fields);
		echo 'Upgraded to version 4<br />';
	}

	public function down()
	{
		$this->dbforge->drop_column('user','user_location');
		$this->dbforge->drop_column('user','user_locale');
		$this->dbforge->drop_column('app','app_banner');
		echo 'Downgraded to version 3<br />';
	}
}