<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript">
	var base_url = '<?php echo base_url();?>';
	var user_id = '<?php echo $user['user_id'];?>';
	var twitter_enable = '<?php echo $twitter_checked ? 1 : 0;?>';
</script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/share/main.js';?>"></script>
<input type="checkbox" class="cb-share-twitter" <?php echo $twitter_checked ? 'checked' : '';?>>Share this on twitter</input>
<input type="checkbox" class="cb-share-facebook" <?php echo $facebook_checked ? 'checked' : '';?>>Share this on facebook</input>
<textarea class="ta-share-message"><?php echo $share_message;?></textarea>
<input type="button" class="bt-share-submit" value="Send" />
<?php
	