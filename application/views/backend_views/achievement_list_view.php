<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Manage Achievements</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Manage Apps</h1>
<?=anchor('backend/new_achievement_info/', 'Add New Achievement', 'title="add achievement to platform"');?>
 | <?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Achievements</h1>
<ul>
<?php
	foreach ($achievement_list as $achievement) {
		
		echo "<li><b>" . anchor('backend/edit_achievement_info/'.$achievement['_id'], $achievement['info']['name'], 'title="edit this achievement information"') . "</b>";
		echo "<br /> description: ".$achievement['info']['description'];
		echo "<br /> app id: ".$achievement['app_id'];
		echo "<br /> app_install_id: ".issetor($achievement['app_install_id']);
		echo "<br /> page_id: ".issetor($achievement['page_id']);
		echo "<br /> campaign_id: ".issetor($achievement['campaign_id']);
		echo "<br /> criteria string:<ul>";
		if(isset($achievement['info']) && isset($achievement['info']['criteria_string'])){
			foreach($achievement['info']['criteria_string'] as $criteria){
				echo "<li>".$criteria."</li>";
			}
		}
		echo "</ul>";
		
		echo "criteria:<ul>";
		if(isset($achievement['criteria'])){
			foreach($achievement['criteria'] as $criteria => $count){
				echo "<li>".$criteria." >= ".$count."</li>";
			}
		}
		echo "</ul>";
		
		echo "</li><br/>";
		
	}
?>
</ul>

</body>
</html>