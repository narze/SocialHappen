<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Home</title>
</head>
<body> 
<h1>Home</h1>
<? if($authenticate){ ?>
	<p>
	You have already logged in.<br />
	<?=anchor('/admin', 'Admin Dashboard', 'title="go to Admin Dashboard"');?>
	</p>
	<p><?=anchor('sh_sitemap','Sitemap');?></p>
<?}else{?>
	<p>
	<div id="fb-root"></div>
	<script src="http://connect.facebook.net/en_US/all.js" type="text/javascript"></script>
	<script type="text/javascript">
		FB.init({appId: '<?php echo $facebook_app_id; ?>', status: true, cookie: true, xfbml: true});
	
		function fblogin() {
            FB.login(function(response) {
                if (response.session) {
                    window.location = '<? echo site_url('connect/collect_data').'/'; ?>';
                } else {
                    
                }
            }, {perms:'<? echo $facebook_default_scope ; ?>'});
        }
				
	</script>
	<button type="button" onclick="fblogin();">Facebook Login</button>
	</p>
<?}?>
</body>
</html>