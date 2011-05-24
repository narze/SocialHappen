<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>App</title>
</head>
<body>
<div id="appProfile"><h1>App Profile</h1></div>
<div id="appCampaigns"><h1>App Campaigns</h1></div>
<div id="appUsers"><h1>App user activities</h1></div>

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
	
		$.getJSON("<?php echo base_url()."app/json_get_profile/{$app_install_id}"; ?>",function(json){
			for(i in json){
				$("#appProfile").append(
					makeList(json[i])
				);
			}
		});
		
		$.getJSON("<?php echo base_url()."app/json_get_campaigns/{$app_install_id}"; ?>",function(json){
			for(i in json){
				$("#appCampaigns").append(
					"<div class='appCampaigns'>" + i + makeList(json[i]) +"</div>"
				);
			}
		});
		
		$.getJSON("<?php echo base_url()."app/json_get_users/{$app_install_id}"; ?>",function(json){
			for(i in json){
				$("#appUsers").append(
					"<div class='appUsers'>" + i + makeList(json[i]) + "</div>"
				);
			}
		});
	});
</script>
</body>
</html>