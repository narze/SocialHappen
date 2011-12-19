<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script>
	var base_url = "<?php echo base_url(); ?>";
	<?php if(isset($vars)) :
		foreach($vars as $name => $value) :
			echo "var {$name} = '{$value}';\n";
		endforeach; 
	endif; ?>
</script>
<div id="fb-root"></div>
<script type="text/javascript">
	window.fbAsyncInit = function() {
		FB.init({
			appId  : '<?php echo $facebook_app_id; ?>',
			channelURL : '<?php echo $facebook_channel_url;?>',
			status : true,
			cookie : true,
			xfbml  : true,
			oauth : true
		});
		send({sh_message:'loaded'});
	};	
	
	(function(d){
     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     d.getElementsByTagName('head')[0].appendChild(js);
   }(document));
</script>
<script src="<?php echo base_url().'assets/js/xd/xd.js'; ?>" type="text/javascript"></script>