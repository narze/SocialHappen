<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Campaign</title>
</head>
<body>
<div id="campaignProfile"><h1>Campaign Profile</h1></div>
<div id="stats"><h1>Stats (soon)</h1></div>
<div id="campaignUsers"><h1>Campaign user activities</h1></div>

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
	
		$.getJSON("<?php echo base_url()."campaign/json_get_profile/{$campaign_id}"; ?>",function(json){
			for(i in json){
				$("#campaignProfile").append(
					makeList(json[i])
				);
			}
		});
		
		$.getJSON("<?php echo base_url()."campaign/json_get_users/{$campaign_id}"; ?>",function(json){
			for(i in json){
				$("#campaignUsers").append(
					"<div class='campaignUsers'>" + i + makeList(json[i]) + "</div>"
				);
			}
		});
	});
</script>
</body>
</html>