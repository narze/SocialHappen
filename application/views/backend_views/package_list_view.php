<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Packages</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Packages</h1>
<?=anchor('backend/add_new_package/', 'Add New Package', 'title="add new package to platform"');?>
 | <?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Packages List</h1>
<p>total packages: <?php echo $total_package; ?></p>
<p><?php echo $pagination; ?></p>
<ul>
	<?php 
		foreach($package_list as $package){
			echo '<li><b>'.anchor('backend/edit_package/'.$package['package_id'], $package['package_name'], 'title="view package detail"').'</b>
			<br/><b>Package ID:</b> '. $package['package_id'].'
			</li>';
		}
	?>
	
</ul>
<p><?php echo $pagination; ?></p>
</body>
</html>