<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_IPv6_support extends CI_Migration {

	public function up(){
		$this->dbforge->modify_column('sessions', array('ip_address' => array('type' => 'varchar', 'constraint' => '45')));
		echo 'Upgraded to version 5<br />';
	}

	public function down()
	{
		$this->dbforge->modify_column('sessions', array('ip_address' => array('type' => 'varchar', 'constraint' => '16')));
		echo 'Downgraded to version 4<br />';
	}
}