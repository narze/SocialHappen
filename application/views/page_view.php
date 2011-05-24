<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Page</title>
</head>
<body>
<div id="pageProfile"><h1>Page Profile</h1></div>
<div id="installedApps"><h1>Installed Apps</h1></div>
<div id="stats"><h1>Stats (soon)</h1></div>
<div id="pageCampaigns"><h1>Page Campaigns</h1></div>
<div id="pageUsers"><h1>Page user activities</h1></div>

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
	
		$.getJSON("<?php echo base_url()."page/json_get_profile/{$page_id}"; ?>",function(json){
			for(i in json){
				$("#pageProfile").append(
					makeList(json[i])
				);
			}
		});
		
		$.getJSON("<?php echo base_url()."page/json_get_installed_apps/{$page_id}"; ?>",function(json){
			for(i in json){
				$("#installedApps").append(
					"<div class='installedApps'>" + i + makeList(json[i]) +"</div>"
				);
			}
		});
		
		$.getJSON("<?php echo base_url()."page/json_get_campaigns/{$page_id}"; ?>",function(json){
			for(i in json){
				$("#pageCampaigns").append(
					"<div class='pageCampaigns'>" + i + makeList(json[i]) + "</div>"
				);
			}
		});
		
		$.getJSON("<?php echo base_url()."page/json_get_users/{$page_id}"; ?>",function(json){
			for(i in json){
				$("#pageUsers").append(
					"<div class='pageUsers'>" + i + makeList(json[i]) + "</div>"
				);
			}
		});
	});
</script>
</body>
</html>