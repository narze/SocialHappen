<div class="bg-container" style="background-image: url('<?php echo $challenge['detail']['image'] ? $challenge['detail']['image'] : base_url('assets/images/default/challenge.png'); ?>')"></div>
<div class="dimmer"></div>

<div class="content">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span3">&nbsp;</div>
			<div class="span6">
				<div class="chalenge-card">
					<div class="challenge-banner">
						<div class="challenge-image" style="background-image: url('<?php echo $challenge['detail']['image'] ? $challenge['detail']['image'] : base_url('assets/images/default/challenge.png'); ?>');" alt="<?php echo htmlspecialchars($challenge['detail']['name']);?>"></div>
						<div class="challenge-reward-badge">[[Reward]]</div>
					</div>
					<div class="challenge-info">
						<h2 class="challenge-name"><?php echo htmlspecialchars($challenge['detail']['name']);?> <small class="challenge-by">by <?php echo $company_name;?></small></h2>
						<p class="challenge-desc"><?php echo htmlspecialchars($challenge['detail']['description']); ?></p>
						<div class="challenge-time"><i class="icon-time"> [[5 days left]]</i></div>
						<hr class="divider" />
						<ul class="action-list">
							<?php foreach($challenge['criteria'] as $key => $criteria) : ?>
								<li class="action-item action-item-feedback">
									<div class="action-name"><?php echo htmlspecialchars($criteria['name']); ?></div>
									<span class="action-type">[[<?php echo htmlspecialchars($criteria['query']['action_id']); ?>]]</span>
								</li>
							<?php endforeach; ?>
						</ul>
						<div class="reward-item">
							<div class="reward-item-image" style="background-image: url('<?php echo htmlspecialchars($challenge['reward_items'][0]['image']); ?>')"></div>
							<div class="reward-item-name"><?php echo htmlspecialchars($challenge['reward_items'][0]['name']); ?></div>
						</div>
					</div>
				</div>
			</div>
			<div class="span3">
				<div class="download">
					<a href="https://itunes.apple.com/us/app/socialhappen/id586002902?ls=1&mt=8" ><img src="<?php echo base_url('assets/images/download-on-appstore.png'); ?>" alt=""></a>
				</div>
			</div>
		</div>
	</div>
</div>
