<?php 
	if(!issetor($challenge_id)){
		$id = $challenge['_id']; $challenge_id = $id->{'$id'};
	}
?>
<div class="challenge" data-challenge-id="<?php echo $challenge_id;?>">

	<div class="challenge-name"><?php echo anchor('settings/page_challenge/update/'.$page_id.'?challenge_id='.$id,$challenge['detail']['name'],'class="update-challenge"');?></div>
	<div class="challenge-start-date"><?php echo $challenge['start_date'];?> - </div>
	<div class="challenge-end-date"><?php echo $challenge['end_date'];?></div>
	<?php 
	// echo '<pre>';
	// var_export($challenge);
	// echo '</pre>';
	?>
</div>