<link href="<?php echo base_url();?>assets/css/common/api_app_bar.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript">
	var base_url = '<?php echo base_url();?>';
	var user_id = '<?php echo $user['user_id'];?>';
	var twitter_enable = '<?php echo $twitter_checked ? 1 : 0;?>';
</script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/share/main.js';?>"></script>
<body style="min-height:500px">
	<div class="popup-fb"><?php 
		if(isset($share_result)) 
		{
			if(isset($share_result['twitter'])) { ?><div class="notice success">Shared on twitter.</div><?php }
			if(isset($share_result['facebook'])) { ?><div class="notice success">Shared on facebook.</div><?php }
			if(isset($share_result['error'])) { ?><div class="notice error"><?php echo $share_result['error']; ?></div><?php }
		} 
		else 
		{ ?>
			<h3>Link</h3>
			<div class="sh-share-link notice success" style="word-break: break-word;">
				<?php echo $share_link;?>
			</div><?php
			$attributes = array('class' => '', 'id' => 'share-submit');
			echo form_open('share/share_submit/'.$app_install_id.'/'.$campaign_id, $attributes); ?>
			<input type="hidden" id="share_link" name="share_link" value="<?php echo $share_link;?>" />
			
			<p>
				<h3>Message</h3>
				<?php echo form_error('message'); ?>				
				<?php echo form_textarea( array( 'name' => 'message', 'style' => 'width:380px;height:80px;margin-left:5px;color: #454545;border: 1px solid #C5C5C5;box-shadow: inset 0 1px 1px #e4e4e4;padding: 10px 5px;resize: vertical;', 'value' => $share_message ) ); ?>
			</p>
			<p>
				<?php echo form_error('twitter'); ?>
				<?php echo form_error('facebook'); ?>
				<?php if($sharebutton['twitter_button']) { ?>
				<input type="checkbox" id="twitter" name="twitter" value="1" class="cb-share-twitter" <?php echo set_checkbox('twitter', '1', $twitter_checked); ?>> 
				<label for="twitter">Twitter</label>
				<?php } ?>
				<?php if($sharebutton['facebook_button']) { ?>
				<input type="checkbox" id="facebook" name="facebook" value="1" class="cb-share-facebook" <?php echo set_checkbox('facebook', '1', $facebook_checked); ?>> 		   
				<label for="facebook">Facebook</label>
				<?php } ?>
			</p>

			<div style="text-align:right;height:50px">
				<input type="submit" name="submit-form" value="Share" style="display:none">
				<div class="sh-sharebutton submit"></div>
			</div>

			<?php echo form_close(); ?><?php 
		} ?>
	</div>
</body>