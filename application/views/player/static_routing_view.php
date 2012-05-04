<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>SocialHappen</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/common/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/common/responsive-min.css">
	<script src="<?php echo base_url(); ?>assets/js/common/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/common/bootstrap.min.js"></script>
	<?php $this->load->view('player/static_ga'); ?>
</head>
<body>
	<?php echo $header; ?>
	<?php echo $static_fb_root;?>

	<div style="width:100%">
		<div id="progress_bar" style="margin:0 auto;width:400px;display:none;">
				<p style="text-align:center">Loading...</p>
				<div class="progress progress-striped progress-info active">
					<div class="bar" style="width: 100%;"></div>
				</div>
			</div>
		<div class="box-overlay" style="z-index:100;display: none;position: absolute;	top:0;	left:0;	width:100%;	height:1500px;	background-color: transparent;	background-color: rgba(200, 200, 200, 0.6);	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#99FFFFFF,endColorstr=#99FFFFFF);	zoom: 1;"></div>

	</div>

	<script>
		var user_facebook_id = 0;
		var fb_loaded = false;

		var facebook_image = '';
		var facebook_name = '';
		var facebook_email = '';

		function allow_facebook_login(){
			fb_loaded = true;
		}

		function fbcallback(data){
			console.log(data);
			user_facebook_id = data.id;


			check_user();
		}

		function check_user(){
			$('#box-overlay').show();
			$('#progress_bar').show();
			jQuery.ajax({
				url: '<?php echo base_url(); ?>player/static_user_check',
				type: "POST",
				data: {
					user_facebook_id: user_facebook_id
				},
				dataType: "json",
				success:function(data){
					console.log(data);
					$('#progress_bar').hide();

					if(data.result=='ok'){
						<?php if(!$true_app_data) {?>
							self.location.href='<?php echo base_url(); ?>player/static_page?app_data=<?php echo $app_data?>&dashboard=true';
						<?php }else{ ?>
							self.location.href='<?php echo base_url(); ?>player/static_play_app_trigger?app_data=<?php echo $app_data; ?>';
						<?php } ?>	
					}else{
						self.location.href='<?php echo base_url(); ?>player/static_signup?app_data=<?php echo $app_data?>';
					}

					
				}
			});
		}

		jQuery(document).ready(function(){
			
			

		});



	</script>
</body>
</html>