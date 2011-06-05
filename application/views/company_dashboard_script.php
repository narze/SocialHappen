<script>

//show apps in page
function view_page_app(page_id){
	$("#page-installed-app-list").show();
	$("#page-installed-app-list").css("height",200);
	$("#company-installed-page-list").css("height",200);
	$("#company-available-page-list").hide();
	$("#page-available-app-list").show();
	$("#left-panel-tab-header").html('Available Apps');
	show_installed_app_in_page(page_id);
	show_available_app_in_page(page_id);
}

//show installed apps in company
function show_installed_app_in_company(){
	jQuery.ajax({
	    url: "<?php echo base_url()."company/json_get_installed_apps/{$company_id}"; ?>",
	    dataType: "json",
	    beforeSend: function(){
			$("#company-installed-app-list").html("");
	        $('#company-app-tab .loading').show();
	    },
		success: function(json) {
            for(i in json){
				$("#company-installed-app-list").append(
					"<li class='ui-state-default'>" + json[i].app_name +"</li>"
				);
			}
			$( "#company-installed-app-list" ).droppable({
				drop: function(e, ui) {
				}
			}).sortable({
				revert: true
			});				
			//amount of installed app
			$("#company-detail-installed-app").html(
				"Installed app:" + json.length
			);
			$('#company-app-tab .loading').hide();
        },
	});
}

//show installed pages in company
function show_installed_page_in_company(){
	jQuery.ajax({
	    url: "<?php echo base_url()."company/json_get_pages/{$company_id}"; ?>",
	    dataType: "json",
	    beforeSend: function(){
			$("#company-installed-page-list").html("");
	        $('#company-page-tab .loading').show();
	    },
		success: function(json) {
            for(i in json){
				$("#company-installed-page-list").append(
					"<li class='ui-state-default'>" + json[i].page_name +"<div class='view_app_link'><a href='javascript:view_page_app("+json[i].page_id+")' style='text-decoration:underline;'>view app</a></div></li>"
				);
			}
			$( "#company-installed-page-list" ).droppable({
				drop: function(e, ui) {
				}
			}).sortable({
				revert: true
			});		
			//amount of installed page
			$("#company-detail-installed-page").html(
				"Installed page:" + json.length
			);
	        $('#company-page-tab .loading').hide();
        },
	});
}

//show installed apps in page
function show_installed_app_in_page(page_id){
	//get installed pages
	jQuery.ajax({
	    url: "<?php echo base_url()."company/json_get_installed_apps/"; ?>" + page_id,
	    dataType: "json",
	    beforeSend: function(){
			$("#page-installed-app-list").html("");
	        $('#company-page-tab .loading').show();
	    },
		success: function(json) {
            for(i in json){
				$("#page-installed-app-list").append(
					"<li class='ui-state-default'>" + json[i].app_name + "</li>"
				);
			}
			$( "#page-installed-app-list" ).droppable({
				drop: function(e, ui) {
				}
			}).sortable({
				revert: true
			});
			$('#company-page-tab .loading').hide();
        },
	});
}

//show company's available apps
function show_available_app_in_company(){	
	$("#page-available-app-list").hide();
	$("#company-available-page-list").hide();
	$("#page-installed-app-list").hide();
	$("#company-installed-app-list").css("height",400);
	$("#company-installed-app-list").show();
	jQuery.ajax({
	    url: "<?php echo base_url()."company/json_get_not_installed_apps/{$company_id}"; ?>",
	    dataType: "json",
	    beforeSend: function(){
			$("#company-available-app-list").html("");
	        $('#left-panel-tab .loading').show();
	    },
		success: function(json) {
            for(i in json){
				$("#company-available-app-list").append(
					"<li class='draggable ui-state-highlight'>" + json[i].app_name +"</li>"
				);
			}
			$("#company-available-app-list li.draggable").draggable({
	            connectToSortable: "#company-installed-app-list",
				helper: "clone",
				revert: "invalid"
	        });
			$('#left-panel-tab .loading').hide();
        },
	});
}

//show company's available pages
function show_available_page_in_company(){
	jQuery.ajax({
	    url: "<?php echo base_url()."user/json_get_facebook_pages_owned_by_user"; ?>",
	    dataType: "json",
	    beforeSend: function(){
			$("#company-available-page-list").html("");
	        $('#left-panel-tab .loading').show();
	    },
		success: function(json) {
			json=json.data;
			for(i in json){
				$("#company-available-page-list").append(
					"<li class='draggable ui-state-highlight'>" + json[i].name +"</li>"
				);
			}
			$("#company-available-page-list li.draggable").draggable({
	            connectToSortable: "#company-installed-page-list",
				helper: "clone",
				revert: "invalid"
	        });
			$('#left-panel-tab .loading').hide();
        },
	});
}

//show page's available apps
function show_available_app_in_page(page_id){
	jQuery.ajax({
	    url: "<?php echo base_url()."company/json_get_not_installed_apps/{$company_id}/"; ?>" + page_id,
	    dataType: "json",
	    beforeSend: function(){
			$("#page-available-app-list").html("");
	        $('#left-panel-tab .loading').show();
	    },
		success: function(json) {
            for(i in json){
				$("#page-available-app-list").append(
					"<li class='draggable ui-state-highlight'>" + json[i].app_name +"</li>"
				);
			}
			$("#page-available-app-list li.draggable").draggable({
	            connectToSortable: "#page-installed-app-list",
				helper: "clone",
				revert: "invalid"
	        });
			$('#left-panel-tab .loading').hide();
        },
	});
}
$(function() {
	//get company detail
	$.getJSON("<?php echo base_url()."company/json_get_profile/{$company_id}"; ?>",function(json){
		var company_detail=json;
		//company name
		$("#company-detail").append(
			"<li>" + company_detail.company_name +"</li>"
		);
		//company address
		$("#company-detail").append(
			"<li>Company Address:" + company_detail.company_address +"</li>"
		);
		//company telephone
		$("#company-detail").append(
			"<li>Telephone:" + company_detail.company_telephone +"</li>"
		);
		//company email
		$("#company-detail").append(
			"<li>Email:" + company_detail.company_email +"</li>"
		);
		//company image
		$("#company-detail").append(
			"<li>Image:" + company_detail.company_image +"</li>"
		);
	});
	
	//get installed pages
	show_installed_page_in_company();
	//get installed apps
	show_installed_app_in_company();
	
	$("#company-installed-page-list li").live("mouseover", function() {
		$(this).find(".view_app_link").show();
	});
	$("#company-installed-page-list li").live("mouseout", function() {
		$(this).find(".view_app_link").hide();
	});

	//get company available pages
	show_available_page_in_company();
	
	$( "ul, li" ).disableSelection();

	$( "#right-panel-tabs" ).tabs({
		select: function(event, ui) {
			switch (ui.index) {
				case 0:
				// page tab selected
				$("#company-available-app-list").hide();
				$("#company-available-page-list").show();
				$("#left-panel-tab-header").html('Available Pages');
				//get company available pages
				show_available_page_in_company();	
				//get installed pages
				show_installed_page_in_company();
				break;
				case 1:
				// app tab selected
				$("#company-available-page-list").hide();
				$("#company-available-app-list").show();
				$("#left-panel-tab-header").html('Available Apps');
				//get company available apps
				show_available_app_in_company();
				//get installed apps
				show_installed_app_in_company();
				break;
			}
		}
	});
	$( "#left-panel-tabs" ).tabs();
});
</script>