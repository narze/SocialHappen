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

	for($i = count($achievement_list) - 1; $i >= 0; $i--){
		$achievement = $achievement_list[$i];
		echo "<li><b>achievement_id: " . anchor('backend/edit_achievement_info/'.$achievement['_id'], $achievement['_id'], 'title="edit this achievement information"') . "</b>";
		echo "<br /> <b>name:</b> ".$achievement['info']['name'];
		echo " <b>description:</b> ".$achievement['info']['description'];
		echo "<br /> <b>app id:</b> ".$achievement['app_id'];
		echo " <b>app_install_id:</b> ".issetor($achievement['app_install_id']);
		echo " <b>page_id:</b> ".issetor($achievement['page_id']);
		echo " <b>campaign_id:</b> ".issetor($achievement['campaign_id']);
		echo "<br /> <b>criteria string:</b><ul>";
		if(isset($achievement['info']) && isset($achievement['info']['criteria_string'])){
			foreach($achievement['info']['criteria_string'] as $criteria){
				echo "<li>".$criteria."</li>";
			}
		}
		echo "</ul>";
		
		echo "<b>criteria:</b><ul>";
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