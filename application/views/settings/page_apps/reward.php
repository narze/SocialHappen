<div id="reward">
	<p><a class="add-reward-item">Add reward item</a></p>
	<div class="reward-item-list"></div>
    <?php if(issetor($reward_items)) :
    	foreach($reward_items as $reward_item) : 
    		$this->load->view('settings/page_apps/reward_item', array('reward_item'=>$reward_item));?>
    	
	<?php endforeach;
		else :
			echo 'u have no reward now';
		endif;
      ?>
</div>