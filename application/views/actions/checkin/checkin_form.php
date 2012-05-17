
<style type="text/css">	
	.ui-autocomplete{
		background: #ffffff;
		cursor: default;
	}
</style>
<?php echo $static_fb_root;?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span4">&nbsp;</div>
		<div class="span4 well">
		<h1><?php echo $action_data['data']['checkin_welcome_message']; ?> <?php echo $user['user_first_name']?></h1>
		<div>
			<div class="loading-div" style="height:36px;display:block;">
				<div id="loading-box" style="display:none;"><img src="<?php echo base_url().'assets/images/loading.gif'; ?>" /></div>
			</div>
			<form class="form-horizontal" name="" action="<?php echo base_url('actions/checkin/add_user_data_checkin'); ?>" method="post">
				<fieldset>
					<legend><?php echo $action_data['data']['checkin_challenge_message']; ?></legend>
					<div>
						<div class="control-group">
							<a href="<?php echo current_url().'?code='.$action_data['hash'].'&basic_view=true'; ?>">Try basic view</a>
						</div>
						<div class="control-group">
							<label class="control-label" for="checkin_facebook_place_name">Do Check-In at</label>
							<div class="controls">
								<?php echo $action_data['data']['checkin_facebook_place_name']; ?>
							</div>
						</div>
						<div class="control-group">

							<div class="control-group" id="search-place-normal"> 
								<label class="control-label" for="autocomplete">autocomplete location search</label>
								<div class="controls">
									<input type="text" name="search_name" id="search_name" onkeypress="return noenter()" />
								</div>
							</div>
						
						</div>
						<?php if($action_data['data']['checkin_min_friend_count']>0): ?>
							<div class="control-group">
								<label class="control-label" for="checkin_min_friend_count">With at least</label>
								<div class="controls">
									<?php echo $action_data['data']['checkin_min_friend_count']; ?> of your friends
								</div>
							</div>
						

							<div id="search-friends-normal"> 
								<div id="friend-list">
									<div id="jfmfs-container"></div> 
								</div> 
							</div>
						
							
						<?php endif; ?>
						<div class="control-group">
							<label class="control-label" for="post_message">Message on the post</label>
							<div class="controls">
								<input type="text" name="post_message" />
							</div>
						</div>
					</div>
					<input type="hidden" name="tagged_user_facebook_ids" />
					<input type="hidden" name="facebook_place_id" id="facebook_place_id" />
					<input type="hidden" name="action_data_hash" value="<?php echo $action_data['hash']; ?>" />
					<div class="control-group">
						<button type="submit" class="btn">Submit</button>
					</div>
				<fieldset>
			</form>
		</div>
	</div>
</div>
