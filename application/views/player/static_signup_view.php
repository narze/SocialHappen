<div style="width:100%" class="signup_view">
	<?php if(isset($app_data_array['data']['message']) && isset($app_data_array['data']['link'])) : ?>
		<div class="alert alert-success" style="margin-top:15px;"><a target="_blank" href="<?php echo $app_data_array['data']['link'];?>"><?php echo $app_data_array['data']['message'];?></a></div>
	<?php endif;?>
	<div style="background:url('<?php echo base_url()?>assets/images/player/static/header.png');width:810px;height:300px;margin:0 auto">
		<a class="progress-signup" href="<?php echo base_url('signup');?>" style="position:absolute;cursor:pointer;display:inline-block;margin-top:210px;margin-left:568px;width:179px;height:64px;"></a>
	</div>
	<div style="background:url('<?php echo base_url()?>assets/images/player/static/content1.png');width:810px;height:687px;margin:0 auto">
	</div>
	<div style="background:url('<?php echo base_url()?>assets/images/player/static/footer.png');width:810px;height:250px;margin:0 auto">
		<div style="display:block;padding-top:121px;">
			<a href="https://facebook.com/socialhappen" class="link-page" style="position:absolute;cursor:pointer;display:inline-block;margin-left:176px;width:177px;height:47px;"></a>
			<a href="<?php echo base_url('signup'); ?>" class="progress-signup" style="position:absolute;cursor:pointer;display:inline-block;margin-left:464px;width:177px;height:47px;"></a>
		</div>
	</div>
		<div id="progress_bar" style="margin:0 auto;width:400px;display:none;">
			<p style="text-align:center">Loading...</p>
			<div class="progress progress-striped progress-info active">
				<div class="bar" style="width: 100%;"></div>
			</div>
		</div>

		<div class="signup-result alert" style="margin:0 auto;width:400px;display:none;"></div>

	</div>

</div>
