<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Default_reward_redeem_method extends CI_Migration {

	public function up(){
    $this->load->model('reward_item_model');
    if(!$this->reward_item_model->updateMultiple(array('redeem_method' => array('$exists' => FALSE)), array('$set' => array('redeem_method' => 'in_store')))) {
      echo 'failed';
    }
    echo 'Upgraded to version 10<br />';
	}

	public function down()
	{
		echo 'Downgraded to version 9<br />';
	}
}