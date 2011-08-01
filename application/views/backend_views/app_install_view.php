<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | App Install</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>App : <?php echo $app['app_name']; ?></h1>
<?=anchor('backend/company', 'Back to company', 'title="go back to company"');?> | <?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Information</h1>
<p><b>Detail:</b> <?php echo $app['app_description'];?></p>
<p><b>Install Date:</b> <?php echo $app_install['app_install_date'];?></p>
<p><b>Status:</b> <?php echo $app_install['app_install_status'];?></p>
<p><b>Go to app:</b> <a href="<?php echo $app_url; ?>"><?php echo $app_url;?></a></p>
<p><img src="<?php echo $app['app_image'];?>" /></p>
<h1>Campaigns</h1>
<ul>
	<?php 
		foreach($campaign_list as $campaign){
			//var_dump($campaign);
			echo '<li><b>'.anchor('backend/campaign/'.$campaign['campaign_id'], $campaign['campaign_name'], 'title="view campaign installed detail"').'</b>
			<br/><b>Detail:</b> '.$campaign['campaign_detail'].'
			<br/><b>Status:</b> '.$campaign['campaign_status_name'].'
			<br/><b>Members:</b> '.$campaign['campaign_all_member'].'
			<br/><b>Start Date:</b> '.$campaign['campaign_start_timestamp'].'
			<br/><b>End Date:</b> '.$campaign['campaign_end_timestamp'].'
			<br/><p><img src="'. $campaign['campaign_image'].'" /></p>
			</li>';
		}
	?>
	
</ul>

<h1>Activities</h1>
<ul>
	<?php 
		foreach($activity_list as $activity){
			echo '<li>'.$activity['message'].'</li>';
		}
	?>
</ul>
</body>
</html>