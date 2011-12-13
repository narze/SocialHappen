<div class="popup-fb signup-campaign">
	<h2>Y U NO JOIN CAMPAIGN?</h2>
	<pre><?php print_r($campaign);?></pre>
	
	<div id="signup-form">
		<?php 
			$attributes = array('class' => 'signup-form', 'id' => ''); 
			$url = "tab/signup_campaign_submit/{$app_install_id}/{$campaign_id}";
			echo form_open($url, $attributes); 
		?>
       		<input type="hidden" name="join-campaign" value="1" />
			<input type="submit" value="Join" />
		<?php echo form_close(); ?>
    </div>
</div>