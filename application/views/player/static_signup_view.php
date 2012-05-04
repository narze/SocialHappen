<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>SocialHappen</title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/common/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/common/responsive-min.css">
	<?php $this->load->view('player/static_ga'); ?>
</head>
<body>
	<?php echo $header; ?>
	<?php echo $static_fb_root;?>

	<div style="width:100%" class="signup_view">
		<?php if(isset($app_data_array['data']['message']) && isset($app_data_array['data']['link'])) : ?>
			<div class="alert alert-success" style="margin-top:15px;"><a target="_blank" href="<?php echo $app_data_array['data']['link'];?>"><?php echo $app_data_array['data']['message'];?></a></div>
		<?php endif;?>
		<div style="background:url('<?php echo base_url()?>assets/images/player/static/header.png');width:810px;height:300px;margin:0 auto">
			<div class="progress-signup" style="position:absolute;cursor:pointer;display:inline-block;margin-top:210px;margin-left:568px;width:179px;height:64px;"></div>
		</div>
		<div style="background:url('<?php echo base_url()?>assets/images/player/static/content1.png');width:810px;height:687px;margin:0 auto">
		</div>
		<div style="background:url('<?php echo base_url()?>assets/images/player/static/footer.png');width:810px;height:250px;margin:0 auto">
			<div style="display:block;padding-top:121px;">
				<div class="link-page" style="position:absolute;cursor:pointer;display:inline-block;margin-left:176px;width:177px;height:47px;"></div>
				<div class="progress-signup" style="position:absolute;cursor:pointer;display:inline-block;margin-left:464px;width:177px;height:47px;"></div>
			</div>
		</div>

		<div class="popup-container" style="z-index:1000;width:100%;display:none;position:absolute;">

			<div class="form-horizontal signup-form" style="background:#fff;width:480px;margin:0 auto;padding:0 15px">

				<legend>สมัครสมาชิก SocialHappen ด้วย Facebook Account</legend>

				<div class="control-group" style="margin-bottom:0;">
					<label class="control-label" id="facebook_image_block"></label>
					<div class="controls">
						<p style="padding-top:20px;" id="facebook_name_block"></p>
					</div>
				</div>

				<input type="hidden" class="input-xlarge" name="email" id="input-email" value="" / >
				<input type="hidden" class="input-xlarge" name="firstname" id="input-firstname" value="" / >
				<input type="hidden" class="input-xlarge" name="lastname" id="input-lastname" value="" / >

				<div class="form-actions">
					<button class="btn btn-primary" id="submit-signup">สมัครสมาชิก (รับ 50 แต้ม)</button>
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
	
	<div class="box-overlay" style="z-index:100;display: none;position: absolute;	top:0;	left:0;	width:100%;	height:1500px;	background-color: transparent;	background-color: rgba(200, 200, 200, 0.6);	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#99FFFFFF,endColorstr=#99FFFFFF);	zoom: 1;"></div>

	<script src="<?php echo base_url(); ?>assets/js/common/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/common/bootstrap.min.js"></script>

	<script>	
		var user_facebook_id = 0;
		var fb_loaded = false;
		var firstname = '';
		var lastname = '';

		jQuery(document).ready(function(){
			jQuery('.link-page').click(function(){
				window.top.location = 'http://www.facebook.com/socialhappen';
			});

			jQuery('.progress-signup').click(function(){
				if(fb_loaded){
					//check fb permission status to app
					if(user_facebook_id==0){
						fblogin();
					}else{
						//else -> show signup-form
						show_signup_form();
					}
				}
			});

			jQuery('.box-overlay').click(function(){
				hide_signup_form();
			});

			jQuery('#submit-signup').click(function(){
				
				var email = jQuery('#input-email').val();
				var firstname = jQuery('#input-firstname').val();
				var lastname = jQuery('#input-lastname').val();
				var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

  				if(regex.test(email)){
  					$('.signup-form').hide();
  					$('#progress_bar').show();
					jQuery.ajax({
						url: '<?php echo base_url(); ?>player/static_signup_trigger',
						type: "POST",
						data: {
							app_data : '<?php echo $app_data; ?>',
							user_facebook_id: user_facebook_id,
							email: email,
							firstname: firstname,
							lastname: lastname
						},
						dataType: "json",
						success:function(data){
							$('#progress_bar').hide();

							if(data.result=='ok'){
								//redirect to play_app_trigger
								jQuery('.signup-result').addClass('alert-success').html('สมัครสมาชิกเรียบร้อยแล้ว <a href="<?php echo base_url()?>player/static_play_app_trigger?app_data=<?php echo $app_data; ?>">Continue</a>');
								jQuery('.signup-result').show('slow');
							}else{
								jQuery('.signup-result').addClass('alert-error').html('Sign Up Failed: ' + data.message + ' <a href="<?php echo base_url()?>player/static_play_app_trigger?app_data=<?php echo $app_data; ?>">Continue</a>');
								jQuery('.signup-result').show('slow');
							}
							
						}
					});
				}
				//return false;
			});

		});

		function allow_facebook_login(){
			fb_loaded = true;
		}
	
		function fbcallback(data){
			console.log(data);
			user_facebook_id = data.id;

			var facebook_image ='http://graph.facebook.com/'+user_facebook_id+'/picture';
			var facebook_name = data.name;
			var facebook_email = data.email;
			var facebook_firstname = data.first_name;
			var facebook_lastname = data.last_name;

			jQuery('#facebook_image_block').html('<img src="'+facebook_image+'" alt="" style="background-color:#ccc;width:50px;height:50px;">');
			jQuery('#facebook_name_block').html('<b>'+facebook_name+'</b>');
			jQuery('#input-email').val(facebook_email);
			jQuery('#input-firstname').val(facebook_firstname);
			jQuery('#input-lastname').val(facebook_lastname);

			show_signup_form();
		}
		
		function show_signup_form(){
			jQuery('.box-overlay').show('fast');
			var windowY = window.scrollY;
			var top = 300;
			jQuery('.popup-container').css('top', windowY+top);
			jQuery('.popup-container').show('slow');

		}

		function hide_signup_form(){
			jQuery('.popup-container').hide('fast');
			jQuery('.box-overlay').hide('slow');

		}
	</script>
</body>
</html>