<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Company_credits extends CI_Migration {

	public function up(){
    $fields = array(
      'credits' => array(
        'type' => 'INT',
        'constraint' => 10,
        'default' => 0,
        'null' => FALSE
      ),
    );
    $this->dbforge->add_column('company', $fields);

    echo 'Upgraded to version 11<br />';
  }

  public function down()
  {
    $this->dbforge->drop_column('company', 'credits');
    echo 'Downgraded to version 10<br />';
  }
}