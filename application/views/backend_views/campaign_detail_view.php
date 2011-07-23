<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Campaign</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Campaign : <?php echo $campaign['campaign_name']; ?></h1>
<?=anchor('backend/app_install/'.$campaign['app_install_id'], 'Back to app', 'title="go back to app"');?> | <?=anchor('backend/company', 'Back to company', 'title="go back to company"');?> | <?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Information</h1>
<p><b>Detail:</b> <?php echo $campaign['campaign_detail'];?></p>
<p><b>Status:</b> <?php echo $campaign['campaign_status_name'];?></p>
<p><b>Members:</b> <?php echo $campaign['campaign_all_member'];?></p>
<p><b>Start Date:</b> <?php echo $campaign['campaign_start_timestamp'];?></p>
<p><b>End Date:</b> <?php echo $campaign['campaign_end_timestamp'];?></p>
<p><img src="<?php echo $campaign['campaign_image'];?>" /></p>
<h1>Users</h1>
<ul>
	<?php 
		foreach($user_list as $user){
			//var_dump($campaign);
			echo '<li><b>'.anchor('backend/user/'.$user['user_id'], $user['user_first_name'].' '.$user['user_last_name'], 'title="view user detail"').'</b>
			<br/><p><img src="'. $user['user_image'].'" />
			</li>';
		}
	?>
	
</ul>
</body>
</html>