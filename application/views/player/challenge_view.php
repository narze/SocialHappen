<div class="container-fluid">
	<div class="row-fluid">
		<div class="span4">&nbsp;</div>
		<div class="span4">
			<div class="chalenge-card">
			<?php if($challenge) : ?>
				<button type="button" class="btn pull-right"><i class="icon-share-alt"></i>&nbsp;</button>
				<h2 class="challenge-name"><?php echo $challenge['detail']['name'];?></h2>
				<p class="small">By : SocialHappen Start : June 16, 2012 End : July 16,2012</p>

				<hr class="divider" />

				<?php if($challenge_done) : 
					if($is_daily_challenge) : ?>
						<!-- <div class="alert alert-info">
							<b>You've already completed this challenge today, this challellenge will be available for you again on <?php echo $challenge_available_date; ?>.</b>
							<?php if($redeem_pending) { echo ' Redeem your reward at the company.'; }
							else {} // echo ' You have redeem this challenge's reward; ?>
						</div> -->
						<div class="alert alert-info">
							<b>You've already completed this challenge</b>
							<?php if($redeem_pending) { echo ' Redeem your reward at the company.'; }
							else {} // echo ' You have redeem this challenge's reward; ?>
						</div>
					<?php else : ?>
						<div class="alert alert-info">
							<b>You've already completed this challenge</b>
							<?php if($redeem_pending) { echo ' Redeem your reward at the company.'; }
							else {} // echo ' You have redeem this challenge's reward; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<div class="row-fluid">
					<p class="challenge-description "><?php echo $challenge['detail']['description']; ?></p>
				</div>

				<div class="row-fluid">
					<div class="control-group">
						<img class="challenge-image" src="<?php echo $challenge['detail']['image'] ? $challenge['detail']['image'] : base_url('assets/images/default/challenge.png'); ?>" alt="<?php echo $challenge['detail']['name'];?>">
					</div>
				</div>

				<div class="row-fluid">
					<?php if($challenge['start_date']) { ?>
						<div class="control-group"><h6>Start </h6><span id="challenge-start-date"></span></div><?php
					}
					if($challenge['end_date']) { ?>
						<div class="control-group"><h6>End </h6><span id="challenge-end-date"></span> (<span id="challenge-until-end"></span>)</div><?php
					} ?>
				</div>

				<hr class="divider" />

				<div class="row-fluid">
					<label><h6>How to WIN</h6></label>
					<div class="control-group">

					<?php if($challenge_not_started) : ?>
						<div class="challenge-not-started"><span class="badge badge-success">Challenge not yet started.</span></div>
					<?php elseif($challenge_ended) : ?>
						<div class="challenge-ended"><span class="badge badge-success">Challenge ended.</span></div>
					<?php elseif(!$player_logged_in || !$player_challenging) : ?>
							<a id="join-challenge" href="<?php echo base_url().'player/join_challenge/'.$challenge_hash;?>" class="btn btn-primary">Accept challenge</a>
					<?php else : //player logged in and is challenging ?>
							<!-- Challenge Actions -->
							<div class="row-fluid" id="challenge-criteria-list">

								<?php if($challenge['criteria']) :

									foreach($challenge['criteria'] as $key => $criteria) : ?>
										<span class="criteria-item">
											<div class="row-fluid">
												<div class="span2">
													<img class="action-image" style="width:100%;" src="<?php echo isset($criteria['image']) ? $criteria['image'] : base_url('assets/images/default/action.png'); ?>" alt="<?php echo $criteria['name'];?>">
												</div>

												<h3 class="criteria-name span10">
													<a href="<?php echo '#/action/'.$criteria['action_data_id'];?>" class="criteria-link">
														<?php echo $criteria['name']; ?>
													</a>
												</h3>

												<div class="span11 offset1">
													<?php if($challenge_progress[$key]['action_done']) : ?>
														<span class="badge badge-success">Done</span>
													<?php else : ?>
														<!-- <span class="badge">
															<?php echo $challenge_progress[$key]['action_count'].'/'.$criteria['count'];?>
														</span> -->
													<?php endif; ?>
												</div>
											</div>
											<div data-id="<?php echo $criteria['action_data_id'];?>"class="row-fluid criteria-form"></div>
										</span>
									<?php endforeach; 

								endif; ?>

							</div>

					<?php endif; ?>

					</div>

				</div>

				<hr class="divider" />

				<div class="reward row-fluid">
					<label><h6>Rewards</h6></label>
					<div class="control-group">
						<p>-</p>
					</div>
				</div>

				<hr class="divider" />

				<div class="row-fluid">
					<h6>Who joined this challenge</h6>
					<div class="control-group">
						<div class="controls challengers-in-progress"><?php if(!$challengers['in_progress_count']) { echo 'None'; } ?>
						<?php foreach($challengers['in_progress'] as $user) :?>
							<span>
								<img src="<?php echo $user['user_image'];?>" alt="<?php echo $user['user_first_name'];?>" title="<?php echo $user['user_first_name'];?>" />
							</span>
						<?php endforeach; ?>
						</div>
						<?php if($challengers['in_progress_count'] > count($challengers['in_progress'])) :?>
							<button class="btn btn-info load-more-in-progress">Load more</button>
						<?php endif; ?>
					</div>
				</div>

				<div class="row-fluid">
					<div class="control-group">
						<h6>Who completed this challenge</h6>
						<div class="controls challengers-completed"><?php if(!$challengers['completed_count']) { echo 'None'; } ?>
						<?php foreach($challengers['completed'] as $user) :?>
							<span>
								<img src="<?php echo $user['user_image'];?>" alt="<?php echo $user['user_first_name'];?>" title="<?php echo $user['user_first_name'];?>" /> 
							</span>
						<?php endforeach; ?>
						</div>
						<?php if($challengers['completed_count'] > count($challengers['completed'])) :?>
							<button class="btn btn-info load-more-completed">Load more</button>
						<?php endif; ?>
					</div>
				</div>

				<hr class="divider" />

				<div class="row-fluid">
					<h6>Other challenge from SocialHappen</h6>
					<div class="control-group">
						-
					</div>
					<div class="text-right"><button type="text" class="btn">See more</button></div>
					
				</div>

				<?php else : ?>

					<div class="alert alert-error">
						Challenge not found
					</div>

				<?php endif; ?>

			</div>
		</div>
	</div>
</div>

<div class="modal hide fade" id="challenge-complete-modal">
	<div class="modal-header">
	<button class="close" data-dismiss="modal">×</button>
	<h3>Challenge Complete!</h3>
	</div>

	<div class="modal-body">
	<div id="login">
		<div class="row-fluid text-center">
		<div class="well">
			<?php if(isset($challenge_score) && isset($company_score)) : ?>
				<p>You got <span class="badge badge-success"><?php echo $challenge_score; ?></span> points from the challenge, now you have <span class="badge badge-success"><?php echo $company_score;?></span> points in total.</p>
			<?php endif; ?>
			<?php if($challenge_reward) : ?>
				<p> You got this reward : </p>
			<div class="reward-container alert alert-success">
				<div class="reward-image">
				<img src="<?php echo $challenge_reward['image']; ?>" />
				</div>
				<div class="reward-info">
				<span class="reward-name">
					<?php echo $challenge_reward['name']; ?>
				</span>
				<span class="reward-value">
					(<?php echo $challenge_reward['value']; ?>)
				</span>
				<div class="reward-description">
					<?php echo $challenge_reward['description']; ?>
				</div>
				</div>
				<!-- <div class="reward-status">
				<?php echo $challenge_reward['status']; ?>
				</div> -->
			</div>
			<?php else : ?>
				<p> This challenge has no reward </p>
			<?php endif; ?>
		</div>
		</div>
	</div>
	</div>

	<div class="modal-footer">
		<button class="btn btn-primary share-challenge-complete" data-dismiss="modal">Share</button>
	</div>
</div>

<div class="modal hide fade" id="challenge-already-complete-modal">
	<div class="modal-header">
	<button class="close" data-dismiss="modal">×</button>
	<h3>Challenge Already Completed!</h3>
	</div>

	<div class="modal-body">
	<div id="login">
		<div class="row-fluid text-center">
		<div class="well">
			<?php if(isset($challenge_available_date)) : ?>
				<p>You've already completed this challenge. This challellenge will be available for you again on <?php echo $challenge_available_date; ?>.</p>
			<?php endif; ?>
			<?php if($challenge_reward) : ?>
				<p> You got this reward : </p>
			<div class="reward-container alert alert-success">
				<div class="reward-image">
				<img src="<?php echo $challenge_reward['image']; ?>" />
				</div>
				<div class="reward-info">
				<span class="reward-name">
					<?php echo $challenge_reward['name']; ?>
				</span>
				<span class="reward-value">
					(<?php echo $challenge_reward['value']; ?>)
				</span>
				<div class="reward-description">
					<?php echo $challenge_reward['description']; ?>
				</div>
				</div>
			</div>
			<?php else : ?>
				<p> This challenge has no reward </p>
			<?php endif; ?>
		</div>
		</div>
	</div>
	</div>

	<div class="modal-footer">
		<button class="btn btn-primary share-challenge-complete" data-dismiss="modal">Share</button>
	</div>
</div>

<script type="text/template" id="challengers-item-template">
  <span>
    <img src="<%= user_image %>" alt="" /> <%= user_first_name %> 
  </span>
</script>
