<div class="container-fluid">
	<div class="row-fluid">
		<div class="span4">&nbsp;</div>
		<div class="span4 well">
			<?php if($challenge) : ?>
				<div class="page-header">
					<h1 class="challenge-name"><?php echo $challenge['detail']['name'];?></h1>
				</div>

				<?php if($challenge_done) : ?>
					<div class="alert alert-info">
						<b>Challenge complete!</b>
						<?php if($redeem_pending) { echo ' Please show this to merchant'; }
						else {} // echo ' You have redeem this challenge's reward; ?>
					</div>
				<?php endif; ?>

				<div>
					<p>
						<img class="challenge-image" src="<?php echo $challenge['detail']['image'] ? $challenge['detail']['image'] : base_url('assets/images/default/challenge.png'); ?>" alt="<?php echo $challenge['detail']['name'];?>">
					</p>
					<p>Start <?php echo $challenge['start_time']; ?></p>
					<p>End <?php echo $challenge['end_time']; ?></p>
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
							<a href="<?php echo base_url().'login?next='.urlencode($next_url);?>" class="btn btn-primary" id="login-btn">Login</a>
						</div>

					<?php elseif(!$player_challenging) : ?>

						<a href="<?php echo base_url().'player/join_challenge/'.$challenge_hash;?>" class="btn btn-primary" id="join-challenge">Accept challenge</a>
					
					<?php else : //player logged in and is challenging ?>
						<!-- Challenge Actions -->
						<div class="row-fluid" id="challenge-criteria-list">

							<?php if($challenge['criteria']) :

								foreach($challenge['criteria'] as $key => $criteria) : ?>
									<span class="criteria-item">
										<div class="row-fluid span12">
											<p class="span1">
												<img class="action-image" style="width:100%;" src="<?php echo isset($criteria['image']) ? $criteria['image'] : base_url('assets/images/default/action.png'); ?>" alt="<?php echo $criteria['name'];?>">
											</p>

											<h3 class="criteria-name span11">
												<a data-url="<?php echo 'player/get_challenge_action_form/'.$challenge['hash'].'/'.$key;?>" class="criteria-link">
													<?php echo $criteria['name']; ?>
												</a>
											</h3>

											<div class="span11 offset1">
												<?php if($challenge_progress[$key]['action_done']) : ?>
													<span class="badge badge-success">Done</span>
												<?php else : ?>
													<span class="badge">
														<?php echo $challenge_progress[$key]['action_count'].'/'.$criteria['count'];?>
													</span>
												<?php endif; ?>
											</div>

										</div>

										<div class="row-fluid criteria-form">
											
										</div>
									</span>
								<?php endforeach; 

							endif; ?>

						</div>

					<?php endif; ?>
  			</div>

			<?php else : ?>

				<div class="alert alert-error">
					Challenge not found
				</div>

			<?php endif; ?>

		</div>
	</div>
</div>

