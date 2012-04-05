{header}
	<div class="container-fluid">

		<?php 
		if($challenge) 
		{ ?>
			<div class="page-header">
				<h1 class="challenge-name"><?php echo $challenge['detail']['name'];?></h1>
			</div><?php

			if($challenge_done) { ?>
				<div class="alert alert-info">
					<b>Challenge complete!</b>
					<?php if($redeem_pending) { echo ' Please show this to merchant'; }
					else {} // echo ' You have redeem this challenge's reward; ?>
				</div><?php
			} ?>
				

				<div class="row-fluid">
					<p>
						<img class="challenge-image" src="<?php echo $challenge['detail']['image'] ? $challenge['detail']['image'] : base_url('assets/images/default/challenge.png'); ?>" alt="<?php echo $challenge['detail']['name'];?>">
					</p>
					<p><?php echo $challenge['start_time'].' - '.$challenge['end_time']; ?></p>
					<p class="challenge-description "><?php echo $challenge['detail']['description']; ?></p>
				</div>

				<div class="reward row-fluid">
					<p>Reward:</p>

				</div>

				<div class="row-fluid">
					<?php if(!$player_logged_in) : 
						$next_url = "player/challenge/{$challenge_hash}"; ?>
						<div id="login">
							<p id="login-message">Please Login SocialHappen First</p>
							<a href="<?php echo base_url().'player/login?next='.urlencode($next_url);?>" class="btn btn-primary" id="login-btn">Login</a>
						</div>
					<?php elseif(!$player_challenging) : ?>
						<a href="<?php echo base_url().'player/join_challenge/'.$challenge_hash;?>" class="btn btn-primary" id="join-challenge">Accept challenge</a>
					<?php else : //player logged in and is challenging ?>
						<a href="<?php echo base_url().'player/challenge_actions/'.$challenge_hash;?>" class="btn btn-primary">View actions</a><?php
					endif; ?>
				</div><?php

		} else { ?>
			<div class="alert alert-error">
				Challenge not found
			</div><?php
		} ?>

	</div>

