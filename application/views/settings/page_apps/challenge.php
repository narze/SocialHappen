<div id="challenge">
	<h2><span>Challenge List Setting</span></h2>
	<a class="add-challenge" href="<?php echo base_url().'settings/page_challenge/form/'.$page_id;?>">Add challenge</a>	
	<div class="challenge-list">
    <?php if(issetor($challenges)) :
    	foreach($challenges as $challenge) : 
    		$this->load->view('settings/page_apps/challenge_list', array(
    			'challenge' => $challenge
    		));?>
    	
	<?php endforeach;
		else :
			echo '<div class="mt20 mb20"><div class="notice warning">You have no challenge now.</div></div>';
		endif;
      ?>
    </div>
</div>