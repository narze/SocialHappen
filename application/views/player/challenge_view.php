<h1 id="challenge-name"><?php echo $challenge['detail']['name'];?></h1>
<p id="challenge-description"><?php echo $challenge['detail']['description'];?></p>
<img id="challenge-image" src="<?php echo $challenge['detail']['image'];?>" alt="<?php echo $challenge['detail']['name'];?>" />
<ul id="challenge-criteria-list">
	<?php if($challenge['criteria']) 
	{
		foreach($challenge['criteria'] as $key => $criteria) : ?>
		<?php if($challenge_progress) {
			echo '<p>Challenge progress :</p><pre>Debug ';
			var_export($challenge_progress[$key]);
			echo '</pre>';
		} ?>
		<li id="challenge-criteria">
			<?php 
				var_export($criteria);
			?>
			<div class="criteria-name">Name : <?php echo $criteria['name'];?></div>
			<div class="criteria-count">Count : <?php echo $challenge_progress[$key]['action_count'].'/'.$criteria['count'];?></div>
			<div class="criteria-done">Done ? : <?php echo $challenge_progress[$key]['action_done'] ? 'Done' : 'Not done';?></div>
			<?php if($player_logged_in && $player_challenging) : ?>
				<div class="">
					<a type="button" href="<?php echo base_url().'player/challenge_action/'.$challenge['hash'].'/'.$key;?>" class="criteria-link">Do this action</a>
				</div>
			<?php elseif($player_logged_in) : ?>
				<div class="">
					Please join to this challenge below
				</div>
			<?php endif; ?>
		</li><?php 
		endforeach; 
	} ?>
</ul>
<?php if(!$player_logged_in) : 
	$next_url = "player/challenge/{$challenge_hash}";
	?>
	<div id="login">
		<p id="login-message">Please Login SocialHappen First</p>
		<a href="<?php echo base_url().'player/login?next='.urlencode($next_url);?>" id="login-btn">LOGIN</a>
	</div>
<?php elseif(!$player_challenging) : ?>
	<a href="<?php echo base_url().'player/join_challenge/'.$challenge_hash;?>" id="join-challenge">join this challenge</a>
<?php else : //player logged in and is challenging
	if($challenge_done) {
		echo 'Challenge done!!';
		if($redeem_pending) {
			echo ' Please show this to merchant';
		} else {
			// echo ' You have redeem this challenge's reward;
		}
	} else {
		echo 'This challenge is not done yet';
	}
endif;