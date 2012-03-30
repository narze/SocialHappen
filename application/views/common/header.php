<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="SocialHappen" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SocialHappen<?php if (isset($title)) { echo " - $title"; }?></title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo base_url().'assets/js/common/jquery.timeago.js';?>"></script>
<script type="text/javascript">
	<?php if(isset($vars)) :
	foreach($vars as $name => $value) :
		echo "var {$name} = '{$value}';\n";
	endforeach; 
endif; ?>
	var base_url = "<?php echo base_url(); ?>";
	var image_url = base_url + "/assets/images/";
	var user_companies = <?php echo json_encode(issetor($user_companies)); ?>; //not safe
</script>
<link rel="stylesheet" type="text/css"  href="<?php echo base_url()."assets/css/common/smoothness/jquery-ui-1.8.9.custom.css";?>" />
<?php if(isset($style)) :
	foreach($style as $one) :
		if($one){
			list($class, $id) = explode('/', $one, 2);
			echo '<link class="stylesheet '.$class.' '.$id.'" rel="stylesheet" type="text/css"  href="'.base_url().'assets/css/'.$one.'.css" />'."\n";
		}
	endforeach; 
endif; 
if(isset($script)) :
	foreach($script as $one) :
		if ($one) echo '<script type="text/javascript" src="'.base_url().'assets/js/'.$one.'.js"></script>'."\n";
	endforeach; 
endif;
?>

</head>
<body>
	<div id="fb-root"></div>
	<script>
	  window.fbAsyncInit = function() {
		FB.init({appId: '<?php echo $facebook_app_id; ?>', 
			channelURL: '<?php echo $facebook_channel_url;?>', 
			status: true, 
			cookie: true,
			xfbml: true,
		 	oauth: true
		});
		FB.getLoginStatus(function(response) {
		  	window.fblogin = function () {
				FB.login(function(response) {
					if (response.status === 'connected') { //console.log('response',response);
						$.getJSON(base_url+"api/request_login?user_facebook_id=" + response.authResponse.userID
						// +'&access_token='+response.authResponse.accessToken 
						, function(json){ //console.log(json);
							if(json.status != 'OK'){
								window.location.replace(base_url+"home/signup");
							} else {
								<?php if(issetor($next)): ?>
									window.location.replace('<?php echo $next; ?>');
								<?php else : ?>
									window.location.replace(window.location.href+"?logged_in=true");
								<?php endif; ?>
							}
						});
					}
				}, {scope:'<?php echo $facebook_default_scope ; ?>'});
			};
		});
	  };

	  
	 // Load the SDK Asynchronously
	  (function(d){
	     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
	     js = d.createElement('script'); js.id = id; js.async = true;
	     js.src = "//connect.facebook.net/en_US/all.js";
	     d.getElementsByTagName('head')[0].appendChild(js);
	   }(document));
	</script>

	<?php if(isset($bar_view_bootstrap)) { ?>
		<div class="navbar navbar-fixed-top"><?php $this->load->view('bar/bar_view_bootstrap'); ?></div>
	<?php } else { ?>
		<div class="header"><?php $this->load->view('bar/bar_view'); ?></div>
	<?php } ?>