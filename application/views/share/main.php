<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript">
	var base_url = '<?php echo base_url();?>';
	var user_id = '<?php echo $user['user_id'];?>';
	var twitter_enable = '<?php echo $twitter_checked ? 1 : 0;?>';
</script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/share/main.js';?>"></script>
<body>
<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('share/share_submit/'.$app_install_id, $attributes); ?>

<p>
	
        <?php echo form_error('twitter'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="twitter" name="twitter" value="1" class="cb-share-twitter" <?php echo set_checkbox('twitter', '1', $twitter_checked); ?>> 
                   
	<label for="twitter">Twitter</label>
</p> 
<p>
	
        <?php echo form_error('facebook'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="facebook" name="facebook" value="1" class="cb-share-facebook" <?php echo set_checkbox('facebook', '1', $facebook_checked); ?>> 
                   
	<label for="facebook">Facebook</label>
</p> 
<p>
        <label for="message">Message <span class="required">*</span></label>
	<?php echo form_error('message'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => 'message', 'rows' => '5', 'cols' => '80', 'value' => $share_message ) ); ?>
</p>

<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
</body>