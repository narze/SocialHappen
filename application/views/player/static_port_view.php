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
	<!--<div class="container hero-unit">
		<h1>Persuade for signup</h1>
		<h2><a href="">Continue permission then sign up</a></h2>
		<h3><a href="<?php echo base_url()?>welcome/play_app_trigger/?app_data=<?php echo $app_data; ?>">After Signup -> Redirect to Call Play App and go to Port_view</a></h3>
	</div>
	<div class="alert alert-info span12">
		<span class="label label-info">Info</span>
		Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT == 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
	</div>-->

	<div style="width:100%">
		<?php if($app_id!=0){ ?>
			<div class="alert alert-success" style="margin-top:15px;">คุณได้รับคะแนนจาก SocialHappen แล้ว 50 แต้ม!</div>
		<?php } ?>
		<div style="background:url('<?php echo base_url()?>assets/images/player/static/header-blank.png');width:810px;height:300px;margin:0 auto">
		</div>
		
		<div style="background:url('<?php echo base_url()?>assets/images/player/static/box-header1.png');width:810px;height:55px;margin:0 auto">
		</div>
		<div style="background:url('<?php echo base_url()?>assets/images/player/static/box-sub-header1.png');width:810px;height:72px;margin:0 auto">
		</div>

		<div style="background:url('<?php echo base_url()?>assets/images/player/static/box-sub-content1.png');width:810px;height:233px;margin:0 auto">
			<div style="display:block;">
					<div class="show" data-direction="prev" style="position:absolute;cursor:pointer;display:inline-block;margin-top:81px;margin-left:36px;width:22px;height:38px;"></div>
					<div class="item" data-number="1" style="position:absolute;cursor:pointer;display:inline-block;margin-top:11px;margin-left:76px;width:212px;height:213px;"></div>
					<div class="item" data-number="2" style="position:absolute;cursor:pointer;display:inline-block;margin-top:11px;margin-left:302px;width:212px;height:213px;"></div>
					<div class="item" data-number="3" style="background:#fff;position:absolute;display:inline-block;margin-top:11px;margin-left:529px;width:212px;height:213px;"></div>
					<div class="show" data-direction="next" style="position:absolute;cursor:pointer;display:inline-block;margin-top:81px;margin-left:759px;width:22px;height:38px;"></div>
			</div>

		</div>
		<div style="background:url('<?php echo base_url()?>assets/images/player/static/box-sub-footer1.png');width:810px;height:26px;margin:0 auto">
		</div>
		<div style="background:27B573;width:810px;height:200px;margin:0 auto">
			User data & statistics >> <?php print_r($user_data); ?>
		</div>
		<div style="background:url('<?php echo base_url()?>assets/images/player/static/footer-blank.png');width:810px;height:250px;margin:0 auto">
		</div>

	</div>

	<script src="<?php echo base_url(); ?>assets/js/common/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/common/bootstrap.min.js"></script>

	<script>
		var item_url = new Array();
		item_url[1] = 'https://www.facebook.com/SocialHappen/app_125984734199028';
		item_url[2] = 'https://www.facebook.com/SocialHappen/app_299915470082039';
		item_url[3] = 'https://www.facebook.com/SocialHappen/app_299915470082039';
		
		/*item_url[1] = 'https://app2.socialhappen.com/ghost';
		item_url[2] = 'https://apps.socialhappen.com/songkran';
		item_url[3] = 'https://app2.socialhappen,com/';*/

		jQuery(document).ready(function(){
			jQuery('.show').click(function(){
				var direction = jQuery(this).attr('data-direction');
				console.log(direction);
			});

			jQuery('.item').click(function(){
				var number = jQuery(this).attr('data-number');
				console.log(number);

				window.top.location = item_url[number];
			});

		});
	</script>
</body>
</html>