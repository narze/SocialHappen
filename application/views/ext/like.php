<html>
<head>
	<meta name="viewport" content="initial-scale=2.3, user-scalable=no">
</head>
<body>
	<div class="like-bg">
		<div class="like-page-name"><img src="//graph.facebook.com/<?php echo $id ?>/picture" alt=""> <?php echo $page_name ?></div>
		<div class="like-msg"><?php if(isset($msg)) echo $msg; ?></div>
		<div class="like-btn">
			<fb:like href="<?php echo $url ?>" width="276" show_faces="true" font="verdana" ref="pwfacebooktest" ></fb:like>
		</div>
	</div>
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

				FB.Event.subscribe('edge.create',
				    function(response) {
				        window.location.href='callback://sh';
				    }
				);
		  };
		 // Load the SDK Asynchronously
		  (function(d){
		     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
		     js = d.createElement('script'); js.id = id; js.async = true;
		     js.src = "//connect.facebook.net/en_US/all.js";
		     d.getElementsByTagName('head')[0].appendChild(js);
		   }(document));
		</script>

	<style>
		.like-bg {
			background-image: url('../assets/images/ext-like.jpg');
			background-repeat: no-repeat;
			background-size: 100%;
			height: 100%;
			width: 100%;
		}
		.like-page-name {
			position: absolute;
			top: 130px;
			left: 28px;
			width: 269px;
			font-size: 14px;
		}
		.like-page-name img {
			vertical-align: middle;
			padding-right: 4px;
			width: 20px;
			height: 20px;
		}
		.like-msg {
			font-size: 10px;
			position: absolute;
			top: 155px;
			left: 28px;
			width: 269px;
		}
		.like-btn {
			position: absolute;
			top: 181px;
			left: 28px;
			width: 269px;
		}
		body {
			margin: 0;
		}
	</style>
</body>
</html>