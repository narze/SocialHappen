<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Test_add extends CI_Migration {

	public function up(){
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
		echo 'Upgraded to 2<br />';
	}

	public function down()
	{
		
			
			
			$this->dbforge->drop_table('test');
			echo 'Downgraded to 1<br />';
		
	}

	
}