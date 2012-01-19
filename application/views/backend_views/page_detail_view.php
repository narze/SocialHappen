<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Page</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Page : <?php echo $page['page_name']; ?></h1>
<?=anchor('backend/company', 'Back to company', 'title="go back to company"');?> | <?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Information</h1>
<p><b>Detail:</b> <?php echo $page['page_detail'];?></p>
<p><b>Members:</b> <?php echo $page['page_all_member'];?></p>
<p><b>Status:</b> <?php echo $page['page_status'];?></p>
<p><b>Facebook:</b> <a href="#<?php echo $page['facebook_page_id'];?>"><?php echo $page['facebook_page_id'];?></a></p>
<p><b>Member limit:</b> <?php echo $page['page_member_limit'] == 0 ? 'Unlimited' : $page['page_member_limit'];?> <?php echo anchor_popup('backend/change_page_member_limit/'.$page['page_id'].'/', 'Change member limit'); ?> </p>
<p><img src="<?php echo $page['page_image'];?>" /></p>
<h1>App</h1>
<p>total apps: <?php echo $total_app; ?></p>
<p><?php echo $pagination; ?></p>
<ul>
	<?php 
		foreach($app_list as $app){
			//var_dump($app);
			//echo '<li>'.anchor('backend/page/'.$page['page_id'], $page['page_name'], 'title="view page detail"').'</li>';
			echo '<li>'.anchor('backend/app_install/'.$app['app_install_id'], $app['app_name'], 'title="view app installed detail"').'
			<br/>Status: '.$app['app_install_status'].'
			
			</li>';
		}
	?>
</ul>
<p><?php echo $pagination; ?></p>
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