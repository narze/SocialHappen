
<style type="text/css">	
	.ui-autocomplete{
		background: #ffffff;
		cursor: default;
	}
	</style>
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
				<p><a href="<?php echo current_url().'?code='.$action_data['hash'].'&basic_view=true'; ?>">Try basic view</a></p>
				<p>Do Check-In at : <?php echo $action_data['data']['checkin_facebook_place_name']; ?></p>
				<p>
					<div id="search-place-normal"> 
						autocomplete location search
						<input type="text" name="search_name" id="search_name" onkeypress="return noenter()" /><br />
					</div>
				</p>
				<?php if($action_data['data']['checkin_min_friend_count']>0): ?>
					<p>With at least <?php echo $action_data['data']['checkin_min_friend_count']; ?> of your friends</p>
					<p>
						<div id="search-friends-normal"> 
							<div id="friend-list">
								<div id="jfmfs-container"></div> 
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
