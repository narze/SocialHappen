<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>User</title>
</head>
<body>
<div id="userProfile"><h1>User Profile</h1></div>
<div id="stats"><h1>Stats (soon)</h1></div>
<div id="userActivitiesApp"><h1>User activities (Apps)</h1></div>
<div id="userActivitiesCampaign"><h1>User activities (Campaigns)</h1></div>

<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
	$(function(){
		function makeList(object){
			var list = '<ul>';
			for (property in object){
				list += "<li><span class='"+property+"-label'>"+property+"</span> : <span class='"+property+"'>"+object[property]+"</span></li>\n";
			}
			return list;
		}
	
		$.getJSON("<?php echo base_url()."user/json_get_profile/{$user_id}"; ?>",function(json){
			for(i in json){
				$("#userProfile").append(
					makeList(json[i])
				);
			}
		});
		
		$.getJSON("<?php echo base_url()."user/json_get_apps/{$user_id}"; ?>",function(json){
			for(i in json){
				$("#userProfile").append(
					makeList(json[i])
				);
			}
		});
		
		$.getJSON("<?php echo base_url()."user/json_get_campaigns/{$user_id}"; ?>",function(json){
			for(i in json){
				$("#userProfile").append(
					makeList(json[i])
				);
			}
		});
	});
</script>
</body>
</html>