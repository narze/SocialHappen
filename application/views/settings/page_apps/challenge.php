<div id="challenge">
	<h2><span>Challenge List Setting</span></h2>

    <div class="tab-head round4">
        <a class="add-challenge btn green fr" href="<?php echo base_url().'settings/page_challenge/form/'.$page_id;?>"><span>+ Add challenge</span></a>
    </div>
	
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