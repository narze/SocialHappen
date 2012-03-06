<?php 
	if(!issetor($challenge_id)){
		$id = $challenge['_id']; $challenge_id = $id->{'$id'};
	}
?>
<div class="challenge" data-challenge-id="<?php echo $challenge_id;?>">
	<div class="section first">
		<?php 
		echo '<pre>';
		var_dump($challenge);
		echo '</pre>';
		echo anchor('settings/page_challenge/update/'.$page_id.'?challenge_id='.$id);
		?>
	</div>
</div>