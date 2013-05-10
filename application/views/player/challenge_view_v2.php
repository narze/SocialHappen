<div class="bg-container" style="background-image: url('<?php echo $challenge['detail']['image'] ? $challenge['detail']['image'] : base_url('assets/images/default/challenge.png'); ?>')"></div>
<div class="dimmer"></div>

<div class="content">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span3">&nbsp;</div>
			<div class="span6">
				<div class="chalenge-card">
				<?php if($challenge) : ?>
					<p class="pull-right">
						<?php if($challenge_ended) : ?>
							<button class="btn btn-primary disabled" disabled="disabled">
								Challenge Ended
							</button>
						<?php elseif($challenge_not_started) : ?>
							<button class="btn btn-primary disabled" disabled="disabled">
								Challenge Not Started
							</button>
						<?php elseif(!$player_logged_in || !$player_challenging) : ?>
						<a id="join-challenge" class="btn btn-primary" href="<?php echo base_url().'player/join_challenge/'.$challenge_hash;?>">Accept challenge</a>
						<?php endif; ?>
					</p>
					<h2 class="challenge-name"><?php echo htmlspecialchars($challenge['detail']['name']);?></h2>
					<p><small>By : <?php echo $company_name;?> Start : <span id="challenge-start-date"></span> End : <span id="challenge-end-date"></span></small></p>


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
						<p class="challenge-description "><?php echo htmlspecialchars($challenge['detail']['description']); ?></p>
					</div>

					<div class="row-fluid">
						<div class="control-group">
							<img class="challenge-image" src="<?php echo $challenge['detail']['image'] ? $challenge['detail']['image'] : base_url('assets/images/default/challenge.png'); ?>" alt="<?php echo htmlspecialchars($challenge['detail']['name']);?>">
						</div>
					</div>



					<hr class="divider" />

					<div class="row-fluid">
						<div class="control-group">
						<?php if($challenge_not_started) : ?>
							<div class="challenge-not-started alert alert-error">Challenge not yet started.</div>
						<?php elseif($challenge_ended) : ?>
							<div class="challenge-ended alert alert-error">Challenge ended.</div>
						<?php else : //player logged in and is challenging ?>
								<!-- Challenge Actions -->
								<div class="row-fluid criteria-list" id="challenge-criteria-list">
									<?php if($challenge['criteria']) : ?>
										<label><h6>How to WIN</h6></label>
										<?php foreach($challenge['criteria'] as $key => $criteria) : ?>
											<div class="criteria-item well">
												<div class="row-fluid">
													<div class="span2">
														<button disabled type="button" class="action-icon btn">Action Icon</button>
													</div>

													<h3 class="criteria-name span10"><?php echo htmlspecialchars($criteria['name']); ?></h3>
													<?php if($player_challenging) {
														if($challenge_progress[$key]['action_done']) { ?>
															<p class="span10">
																<button disabled type="button" class="btn btn-success btn-small">Done</button>
															</p>
														<?php } else { ?>
															<p class="span10">
																<a href="<?php echo '#/action/'.$criteria['action_data_id'];?>" class="criteria-link btn btn-primary btn-small">
																Do it
																</a>
															</p>
															<!-- <span class="badge">
																<?php echo $challenge_progress[$key]['action_count'].'/'.$criteria['count'];?>
															</span> -->
														<?php } ?>

													<?php } ?>
												</div>
												<div data-id="<?php echo $criteria['action_data_id'];?>"class="row-fluid criteria-form"></div>
											</div>
										<?php endforeach; ?>
										<hr class="divider" />
									<?php
									endif; ?>
								</div>
						<?php endif; ?>
						</div>
					</div>

					<div class="reward row-fluid">
						<label><h6>Rewards</h6></label>
						<div class="control-group">
							<p>-</p>
						</div>
					</div>

					<hr class="divider" />

					<div class="row-fluid">
						<label><h6>Who joined this challenge</h6></label>
						<div class="control-group">
							<div class="controls challengers-in-progress"><?php if(!$challengers['in_progress_count']) { echo 'None'; } ?>
							<?php foreach($challengers['in_progress'] as $user) :?>
								<li>
									<img src="<?php echo $user['user_image'];?>" alt="<?php echo $user['user_first_name'];?>" title="<?php echo $user['user_first_name'];?>" /> <span class="user_name"><?php echo "{$user['user_first_name']} {$user['user_last_name']}" ?></span>
								</li>
							<?php endforeach; ?>
							</div>
							<?php if($challengers['in_progress_count'] > count($challengers['in_progress'])) :?>
								<button class="btn btn-info load-more-in-progress">Load more</button>
							<?php endif; ?>
						</div>
					</div>

					<div class="row-fluid">
						<div class="control-group">
							<label><h6>Who completed this challenge</h6></label>
							<div class="controls challengers-completed"><?php if(!$challengers['completed_count']) { echo 'None'; } ?>
							<?php foreach($challengers['completed'] as $user) :?>
								<li>
									<img src="<?php echo $user['user_image'];?>" alt="<?php echo $user['user_first_name'];?>" title="<?php echo $user['user_first_name'];?>" /> <span class="user_name"><?php echo "{$user['user_first_name']} {$user['user_last_name']}" ?></span>
								</li>
							<?php endforeach; ?>
							</div>
							<?php if($challengers['completed_count'] > count($challengers['completed'])) :?>
								<button class="btn btn-info load-more-completed">Load more</button>
							<?php endif; ?>
						</div>
					</div>

					<hr class="divider" />

					<div class="row-fluid">
						<label><h6>Other challenge from SocialHappen</h6></label>
						<div class="control-group">
							-
						</div>
					</div>

					<div class="challenge-footer text-right">
						<button type="text" class="btn">See more</button>
						<?php if($challenge_ended) : ?>
							<button class="btn btn-primary disabled" disabled="disabled">
								Challenge Ended
							</button>
						<?php elseif($challenge_not_started) : ?>
							<button class="btn btn-primary disabled" disabled="disabled">
								Challenge Not Started
							</button>
						<?php elseif(!$player_logged_in || !$player_challenging) : ?>
						<a id="join-challenge" class="btn btn-primary" href="<?php echo base_url().'player/join_challenge/'.$challenge_hash;?>">Accept challenge</a>
						<?php endif; ?>
					</div>

					<?php else : ?>

						<div class="alert alert-error">
							Challenge not found
						</div>

					<?php endif; ?>

				</div>
			</div>
			<div class="span3">
				<div class="download">
					<a href="https://itunes.apple.com/us/app/socialhappen/id586002902?ls=1&mt=8" ><img src="<?php echo base_url('assets/images/download-on-appstore.png'); ?>" alt=""></a>
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
				<?php if(isset($challenge_score) && ($challenge_score > 0) && isset($company_score)) : ?>
					<p>You got <span class="badge badge-success"><?php echo $challenge_score; ?></span> points from the challenge, now you have <span class="badge badge-success"><?php echo $company_score;?></span> points in total.</p>
				<?php endif; ?>
				<?php if($challenge_rewards) : ?>
					<?php if(count($challenge_rewards) === 1) : ?>
						<p>You got this reward : </p>
					<?php else : ?>
						<p>You got these rewards :</p>
					<?php endif; ?>
					<?php foreach($challenge_rewards as $challenge_reward) : ?>
						<div class="reward-container alert alert-success">
							<div class="reward-image">
								<img src="<?php echo $challenge_reward['image']; ?>" />
								</div>
								<div class="reward-info">
								<span class="reward-name">
									<?php echo htmlspecialchars($challenge_reward['name']); ?>
								</span>
								<?php if($challenge_reward['value'] > 0) : ?>
									<span class="reward-value">
										(<?php echo $challenge_reward['value']; ?>)
									</span>
								<?php endif; ?>
								<div class="reward-description">
									<?php echo htmlspecialchars($challenge_reward['description']); ?>
								</div>
								<div class="reward-coupon-link">
									<a class="btn btn-success view-coupon" href="<?php echo base_url('assets/passport/#/profile/'.$user_id.'/coupon/'.get_mongo_id($challenge_reward)); ?>">View Coupon</a>
								</div>
							</div>
							<!-- <div class="reward-status">
							<?php echo $challenge_reward['status']; ?>
							</div> -->
						</div>
					<?php endforeach; ?>
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
				<?php if($challenge_rewards) : ?>
					<?php if(count($challenge_rewards) === 1) : ?>
						<p>You have gotten this reward : </p>
					<?php else : ?>
						<p>You have gotten these rewards :</p>
					<?php endif; ?>
					<?php foreach($challenge_rewards as $challenge_reward) : ?>
						<div class="reward-container alert alert-success">
							<div class="reward-image">
								<img src="<?php echo $challenge_reward['image']; ?>" />
								</div>
								<div class="reward-info">
								<span class="reward-name">
									<?php echo htmlspecialchars($challenge_reward['name']); ?>
								</span>
								<?php if($challenge_reward['value'] > 0) : ?>
									<span class="reward-value">
										(<?php echo $challenge_reward['value']; ?>)
									</span>
								<?php endif; ?>
								<div class="reward-description">
									<?php echo htmlspecialchars($challenge_reward['description']); ?>
								</div>
								<div class="reward-coupon-link">
									<a class="btn btn-success view-coupon" href="<?php echo base_url('assets/passport/#/profile/'.$user_id.'/coupon/'.get_mongo_id($challenge_reward)); ?>">View Coupon</a>
								</div>
							</div>
							<!-- <div class="reward-status">
							<?php echo $challenge_reward['status']; ?>
							</div> -->
						</div>
					<?php endforeach; ?>
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
</div>
<script type="text/template" id="challengers-item-template">
  <li>
    <img src="<%= user_image %>" alt="" /> <span><%= user_first_name %></span>
  </li>
</script>
