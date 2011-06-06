<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Company</title>
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
	<?php echo link_tag("css/smoothness/jquery-ui-1.8.9.custom.css"); ?>
	<script>
		var base_url="<?php echo base_url();?>";
		var company_id=<?php echo $company_id?>;
	</script>
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
	{dragdrop_script}
</head>
<body>
<style>
body{
	font-size: 11px;
}
#company-installed-page-list li,#page-installed-app-list li,#company-available-page-list li,
#company-installed-app-list li,#company-available-app-list li,#page-available-app-list li{
	margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 90px; text-align: center;
}
#company-installed-page-list li:hover,#page-installed-app-list li:hover,#company-available-page-list li:hover,
#company-installed-app-list li:hover,#company-available-app-list li:hover,,#page-available-app-list li:hover{
	cursor: move;
}
#company-installed-page-list,#page-installed-app-list,#company-available-page-list,
#company-installed-app-list,#company-available-app-list,#page-available-app-list{
	list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px;
	height: 400px;
}
#left-panel{
	float: left;
	width: 500px;
}
#right-panel{
	float: right;
	width: 500px;
}
.draggable{
	z-index: 100;
}
.view_app_link{
	display: none;
}
.loading{
	display: none;
}
</style>
<div id="company-detail">
<li id="company-detail-installed-app"></li>
<li id="company-detail-installed-page"></li>
</div>
<div id="main-div" style="height:500px;">
	<div id="left-panel">
		<div id="left-panel-tabs">
			<ul>
				<li><a id="left-panel-tab-header" href="#left-panel-tab">Available Pages</a></li>
			</ul>
			<div id="left-panel-tab">
				<div class="loading"><?php echo img("images/loading.gif");?></div>				
				<ul id="company-available-page-list"></ul>
				<ul id="page-available-app-list" style="display:none;"></ul>
				<ul id="company-available-app-list" style="display:none;"></ul>
			</div>
		</div>
	</div>
	<div id="right-panel">
		<div id="right-panel-tabs">
			<ul>
				<li><a href="#company-page-tab">Page</a></li>
				<li><a href="#company-app-tab">App</a></li>
			</ul>
			<div id="company-page-tab">
				<div class="loading"><?php echo img("images/loading.gif");?></div>
				<ul id="company-installed-page-list"></ul>
				<ul id="page-installed-app-list" style="display:none;"></ul>
			</div>
			<div id="company-app-tab">
				<div class="loading"><?php echo img("images/loading.gif");?></div>
				<ul id="company-installed-app-list"></ul>
			</div>
		</div>
	</div>
</div>
<div id="companyPages"><h1>Pages</h1></div>
<div id="installedApps"><h1>Installed Apps</h1></div>
<div id="pageApps"><h1>Page Apps : <span id="pageId"></span></h1><div id="pageAppsResult"></div></div>
<div id="companyApps"><h1>Company Apps</h1></div>
</body>
</html>