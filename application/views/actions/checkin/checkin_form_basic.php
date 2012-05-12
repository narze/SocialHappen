
<?php echo $static_fb_root;?>
<div>
	<h1><?php echo $action_data['data']['checkin_welcome_message']; ?> <?php echo $user['user_first_name']?></h1>
	<div>
		<div class="loading-div" style="height:36px;display:block;">
			<div id="loading-box" style="display:none;"><img src="<?php echo base_url().'assets/images/loading.gif'; ?>" /></div>
		</div>
		<form name="" action="<?php echo base_url('actions/checkin/add_user_data_checkin'); ?>" method="post">
			<div><?php echo $action_data['data']['checkin_challenge_message']; ?></div>
			<div>
				<p>Do Check-In at : <?php echo $action_data['data']['checkin_facebook_place_name']; ?></p>
				<p>
					<div id="search-place-basic"> 
						search
						<input type="text" name="search_place_basic" id="search_place_basic" onkeypress="return noenter()" />
						<input type="button" class="search_basic_submit" data-id="place" value="Search"> 
						<div id="search_place_basic_result">
						</div>
					</div>
				</p>
				<?php if($action_data['data']['checkin_min_friend_count']>0): ?>
					<p>With at least <?php echo $action_data['data']['checkin_min_friend_count']; ?> of your friends</p>
					<p>
						<div id="search-friends-basic">
							selected friends
							<div id="selected-friends-list">-</div>
							<input type="text" name="search_friends_basic" id="search_friends_basic" onkeypress="return noenter()" />
							<input type="button" class="search_basic_submit" data-id="friends" value="Search"> 
							<div id="search_friends_basic_result">
							</div>
						</div>
					</p>
				<?php endif; ?>
				<p>Message on the post</p>
				<p>
					<input type="text" name="post_message" />
				</p>
			</div>
			<input type="hidden" name="tagged_user_facebook_ids" />
			<input type="hidden" name="facebook_place_id" id="facebook_place_id" />
			<input type="hidden" name="action_data_hash" value="<?php echo $action_data['hash']; ?>" />
			<div>
				<input type="submit" value="submit" />
		</form>
	</div>
</div>
