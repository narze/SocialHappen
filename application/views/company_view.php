<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Company</title>
</head>
<body>
<div id="companyPages"><h1>Pages</h1></div>
<div id="companyApps"><h1>Apps</h1></div>
<div id="availableApps"><h1>Available Apps</h1></div>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
	$(function(){
		$.getJSON("<?php echo base_url()."company/json_company_page_list/{$company_id}"; ?>",function(json){
			for(i in json){
				$("#companyPages").append(
					"<div class='pages'>" + json[i].page_id + " | " + json[i].facebook_page_id + " | "
					 + json[i].company_id + " | " + json[i].page_name + " | " + json[i].page_detail + " | "
					 + json[i].page_all_member + " | " + json[i].page_new_member + " | " + json[i].page_image + "</div>"
				);
			}
		});
		
		$.getJSON("<?php echo base_url()."company/json_company_app_list/{$company_id}"; ?>",function(json){
			for(i in json){
				$("#companyApps").append(
					"<div class='pages'>" + json[i].company_id + " | " + json[i].app_id + " | "
					 + json[i].available_date + "</div>"
				);
			}
		});

	});
</script>
</body>
</html>