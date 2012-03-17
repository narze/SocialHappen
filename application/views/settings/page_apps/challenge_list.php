<?php 
	if(!issetor($challenge_id)){
		$id = $challenge['_id']; $challenge_id = $id->{'$id'};
	}
?>
<div class="challenge" data-challenge-id="<?php echo $challenge_id;?>">
	<div class="section first">
		<div class="challenge">
			<div class="challenge_name">Challenge Name : <?php echo $challenge['detail']['name'];?></div>
			<div class="challenge_start_date">Challenge Start Date : <?php echo $challenge['start_date'];?></div>
			<div class="challenge_end_date">Challenge End Date : <?php echo $challenge['end_date'];?></div>
			<div class="challenge_update"><?php echo anchor('settings/page_challenge/update/'.
				$page_id.'?challenge_id='.$id,'Update','class="update-challenge"');?></div>
		</div>
		<?php 
		// echo '<pre>';
		// var_export($challenge);
		// echo '</pre>';
		?>
	</div>
</div>