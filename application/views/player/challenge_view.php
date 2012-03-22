<h1 id="challenge-name"><?php echo $challenge['detail']['name'];?></h1>
<p id="challenge-description"><?php echo $challenge['detail']['description'];?></p>
<img id="challenge-image" src="<?php echo $challenge['detail']['image'];?>" alt="<?php echo $challenge['detail']['name'];?>" />
<ul id="challenge-criteria-list">
	<?php if($challenge['criteria']) 
	{
		foreach($challenge['criteria'] as $criteria) : ?>
		<li id="challenge-criteria">
			<?php 
				var_export($criteria);
			?>
			<div class="criteria-name">Name : <?php echo $criteria['name'];?></div>
			<div class="criteria-count">Count : <?php echo $criteria['count'];?></div>
			<?php if($player_logged_in) : ?>
				<div class="">
					<a href="#" class="criteria-link">Do this challenge</a>
				</div>
			<?php endif; ?>
		</li><?php 
		endforeach; 
	} ?>
</ul>
<?php if(!$player_logged_in) : 
	$next_url = "player/challenge/{$challenge_id}";
	?>
	<div id="login">
		<p id="login-message">Please Login SocialHappen First</p>
		<a href="<?php echo base_url().'player/login?next='.urlencode($next_url);?>" id="login-btn">LOGIN</a>
	</div>
<?php endif;