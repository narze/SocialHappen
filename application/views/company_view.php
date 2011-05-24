<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Company</title>
</head>
<body>
<div id="companyPages"><h1>Pages</h1></div>
<div id="installedApps"><h1>Installed Apps</h1></div>
<div id="pageApps"><h1>Page Apps : <span id="pageId"></span></h1><div id="pageAppsResult"></div></div>
<div id="companyApps"><h1>Company Apps</h1></div>
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
		
		function ulPageId($selector){
			$selector.find('.page_id').css("text-decoration", "underline").css("color","red");
		}
	
		$.getJSON("<?php echo base_url()."company/json_get_pages/{$company_id}"; ?>",function(json){
			for(i in json){
				$("#companyPages").append(
					"<div class='pages'>" + i + makeList(json[i]) + "</div>"
				);
			}
			ulPageId($("#companyPages"));
		});
		
		$.getJSON("<?php echo base_url()."company/json_get_installed_apps/{$company_id}"; ?>",function(json){
			for(i in json){
				$("#installedApps").append(
					"<div class='installedApps'>" + i + makeList(json[i]) +"</div>"
				);
			}
			ulPageId($("#installedApps"));
		});
		
		$.getJSON("<?php echo base_url()."company/json_get_apps/{$company_id}"; ?>",function(json){
			for(i in json){
				$("#companyApps").append(
					"<div class='companyApps'>" + i + makeList(json[i]) + "</div>"
				);
			}
		});
		
		
		$('.page_id').live('click',function(){
			$page_id = $(this).html();
			$('#pageId').html($page_id);
			$.getJSON("<?php echo base_url()."page/json_get_installed_apps/"; ?>"+$page_id,function(json){
				if(!json.length) {
					$("#pageAppsResult").html(
						"<div class='error'>Not found</div>"
					);
				}
				for(i in json){
					$("#pageAppsResult").html(
						"<div class='pageApps'>" + i + makeList(json[i]) + "</div>"
					);
				}
			})
		});

	});
</script>
</body>
</html>