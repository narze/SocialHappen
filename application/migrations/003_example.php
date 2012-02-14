<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Example extends CI_Migration {
	public function up(){
		echo 'Upgraded to version 3<br />';
	}

	public function down()
	{
		echo 'Downgraded to version 2<br />';
	}

	public function up_example(){
		$this->dbforge->add_key('blog_id', TRUE);
		$this->dbforge->add_field(array(
			'blog_id' => array(
				'type' => 'INT',
				'constraint' => 5,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'blog_title' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
			),
			'blog_description' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
		));
		
		$this->dbforge->create_table('test');
		echo 'Upgraded to 3<br />';
	}

	public function down_example()
	{
		$this->dbforge->drop_table('test');
		echo 'Downgraded to 2<br />';
	}
}