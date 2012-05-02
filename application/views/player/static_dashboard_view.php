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
	<?php echo $static_fb_root;?>

	<div style="width:100%">
		<div class="user-data" style="background:#EEEEEE;width:810px;height:100px;margin:0 auto;margin-bottom:5px">
		</div>
		<div class="played-apps-data" style="background:#EEEEEE;width:810px;height:300px;margin:0 auto;margin-bottom:5px">
		</div>
		<div class="all-apps-data" style="background:#EEEEEE;width:810px;height:300px;margin:0 auto;margin-bottom:5px">
		</div>
		
	</div>

	<script src="<?php echo base_url(); ?>assets/js/common/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/common/bootstrap.min.js"></script>

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

			facebook_image ='http://graph.facebook.com/'+user_facebook_id+'/picture';
			facebook_name = data.name;
			facebook_email = data.email;

			get_user_data();
		}

		function get_user_data(){
			jQuery.ajax({
				url: '<?php echo base_url(); ?>player/static_get_user_data',
				type: "POST",
				data: {
					user_facebook_id: user_facebook_id
				},
				dataType: "json",
				success:function(data){
					console.log(data);
					$('#progress_bar').hide();

					//TODO - place data to the box
					jQuery('.user-data').html(facebook_name);

					var tmp = '<p>Played Apps</P>';
					for(x in data.played_apps){
						app = data.played_apps[x];
						tmp = tmp + app.app_name+'<br>';
					}
					
					jQuery('.played-apps-data').html(tmp);

					var tmp = '<p>Avaliable Apps</P>';
					for(x in data.available_apps){
						app = data.available_apps[x];
						tmp = tmp + app.app_name+'<br>';
					}

					jQuery('.all-apps-data').html(tmp);
				}
			});
		}

		jQuery(document).ready(function(){
			
			

		});



	</script>
</body>
</html>