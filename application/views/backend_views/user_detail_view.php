<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | User</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>User : <?php echo $user['user_id']; ?></h1>
<?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Information</h1>
<p><b>Firstname:</b> <?php echo $user['user_first_name'];?></p>
<p><b>Lastname:</b> <?php echo $user['user_last_name'];?></p>
<p><b>Email:</b> <?php echo $user['user_email'];?></p>
<p><b>Birthdate:</b> <?php echo $user['user_birth_date'];?></p>
<p><b>Gender:</b> <?php echo $user['user_gender'];?></p>
<p><b>About:</b> <?php echo $user['user_about'];?></p>
<p><b>Go to facebook:</b> <a href="https://www.facebook.com/profile.php?id=<?php echo $user['user_facebook_id'];?>"><?php echo $user['user_facebook_id'];?></a></p>
<p><b>Register Date:</b> <?php echo $user['user_register_date'];?></p>
<p><b>Last Seen Date:</b> <?php echo $user['user_last_seen'];?></p>
<p><img src="<?php echo $user['user_image'];?>" /></p>


<h1>Companies</h1>
<ul>
<?php
	foreach ($company_list as $company) {
		echo "<li>
		".anchor('backend/company_detail/'.$company['company_id'], $company['company_name'], 'title="view company detail"')."
		<br/>Detail: ".$company['company_detail']."
		</li>";
	}
?>
</ul>


<h1>App</h1>
<ul>
	<?php 
		foreach($app_list as $app){
			//var_dump($app);
			//echo '<li>'.anchor('backend/page/'.$page['page_id'], $page['page_name'], 'title="view page detail"').'</li>';
			echo '<li>'.anchor('backend/app_install/'.$app['app_install_id'], $app['app_name'], 'title="view app installed detail"').'
			
			</li>';
		}
	?>
</ul>
</body>
</html>