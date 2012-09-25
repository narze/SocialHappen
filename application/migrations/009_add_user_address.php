<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_user_address extends CI_Migration {

	public function up(){
    $fields = array(
      'user_address' => array(
        'type' => 'TEXT',
        'null' => TRUE
      )
    );
    $this->dbforge->add_column('user', $fields);
    echo 'Upgraded to version 9<br />';
	}

	public function down()
	{
		$this->dbforge->drop_column('user','user_address');
		echo 'Downgraded to version 8<br />';
	}
}