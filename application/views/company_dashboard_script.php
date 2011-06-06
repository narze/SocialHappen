<script>
var dropped=false;
var all_app_install_statuses=new Array();
//add app to facebook page
function get_add_app_to_fb_page_link(facebook_app_api_key,facebook_page_id){
	return "http://www.facebook.com/add.php?api_key="+facebook_app_api_key+"&pages=1"+"&page="+facebook_page_id;
}
//show apps in page
function view_page_app(page_id,facebook_page_id){
	$("#page-installed-app-list").show();
	$("#page-installed-app-list").css("height",200);
	$("#company-installed-page-list").css("height",200);
	$("#company-available-page-list").hide();
	$("#page-available-app-list").show();
	$("#left-panel-tab-header").html('Available Apps');
	show_installed_app_in_page(page_id,facebook_page_id);
	show_available_app_in_page(page_id);
}

//show installed apps in company
function show_installed_app_in_company(){
	jQuery.ajax({
	    url: base_url + "company/json_get_installed_apps/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
			$("#company-installed-app-list").html("");
	        $("#company-app-tab .loading").show();
	    },
		success: function(json) {
            for(i in json){
				$("#company-installed-app-list").append(
					"<li class='ui-state-default'>" + json[i].app_name +"</li>"
				);
			}
			$( "#company-installed-app-list" ).droppable({
				drop: function(e, ui) {
					if(!dropped){
						dropped=true;
						var app_id=$(ui.draggable).children('input.app_id').val();
						var app_secret_key=$(ui.draggable).children('input.app_secret_key').val();
						$(ui.draggable).removeClass('draggable');
						jQuery.ajax({
							url: base_url + "app/json_add",
							dataType: "json",
							type: "POST",
							data: ({company_id : company_id, app_id : app_id, app_install_status : 1, page_id : 0 , app_install_secret_key : app_secret_key}),
							success: function(json) {
								if(json.status=="OK") alert("DONE");
								else alert("ERROR");
								show_available_app_in_company();
							},
						});
					}
				},
				accept:"li.draggable"
			}).sortable({
				revert: true,
				stop: function(e,ui){
					dropped=false;
				}
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
	    url: base_url + "company/json_get_pages/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
			$("#company-installed-page-list").html("");
	        $('#company-page-tab .loading').show();
	    },
		success: function(json) {
            for(i in json){
				$("#company-installed-page-list").append(
					"<li class='ui-state-default'>" + json[i].page_name +"<div class='view_app_link'><a href='javascript:view_page_app("+json[i].page_id+","+json[i].facebook_page_id+")' style='text-decoration:underline;'>view app</a></div></li>"
				);
			}
			$( "#company-installed-page-list" ).droppable({
				drop: function(e, ui) {
					if(!dropped){
						dropped=true;
						var fb_page_id=$(ui.draggable).children('input.facebook_page_id').val();
						var page_name=$(ui.draggable).children('input.page_name').val();
						$(ui.draggable).removeClass('draggable');
						jQuery.ajax({
							url: base_url + "page/json_add",
							dataType: "json",
							type: "POST",
							data: ({company_id : company_id, facebook_page_id : fb_page_id, page_name : page_name, page_detail : "", page_all_member : 0, page_new_member : 0 , page_image : ""}),
							success: function(json) {
								if(json.status=="OK"){
									alert("Go to Facebook to complete the action.");
								}
								else alert("ERROR");
								show_available_page_in_company();
							},
						});
					}
				},
				accept:"li.draggable"
			}).sortable({
				revert: true,
				stop: function(e,ui){
					dropped=false;
				}
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
function show_installed_app_in_page(page_id,facebook_page_id){
	//get installed pages
	jQuery.ajax({
	    url: base_url + "page/json_get_installed_apps/" + page_id,
	    dataType: "json",
	    beforeSend: function(){
			$("#page-installed-app-list").html("<h3>Page's installed apps<h3>");
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
					if(!dropped){
						dropped=true;
						var app_id=$(ui.draggable).children('input.app_id').val();
						var app_secret_key=$(ui.draggable).children('input.app_secret_key').val();
						var app_api_key=$(ui.draggable).children('input.app_api_key').val();
						$(ui.draggable).removeClass('draggable');
						jQuery.ajax({
							url: base_url + "app/json_add",
							dataType: "json",
							type: "POST",
							data: ({company_id : company_id, app_id : app_id, app_install_status : all_app_install_statuses['not complete install'][0], page_id : page_id , app_install_secret_key : app_secret_key}),
							success: function(json) {
								if(json.status=="OK"){
									alert("Go to Facebook to complete the action.");
									alert(get_add_app_to_fb_page_link(app_api_key,facebook_page_id));
									window.location=get_add_app_to_fb_page_link(app_api_key,facebook_page_id);
								}
								else alert("ERROR");
								show_available_app_in_page(page_id);
							},
						});
					}
				},
				accept:"li.draggable"
			}).sortable({
				revert: true,
				stop: function(e,ui){
					dropped=false;
				}
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
	    url: base_url + "company/json_get_not_installed_apps/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
			$("#company-available-app-list").html("");
	        $("#left-panel-tab .loading").show();
	    },
		success: function(json) {
            for(i in json){
				$("#company-available-app-list").append(
					"<li class='draggable ui-state-highlight'><input class='app_id' type='hidden' value='" + json[i].app_id + "'/><input class='app_secret_key' type='hidden' value='" + json[i].app_secret_key + "'/><input class='app_api_key' type='hidden' value='" + json[i].facebook_app_api_key + "'/>" + json[i].app_name +"</li>"
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
	    url: base_url + "page/json_get_not_installed_facebook_pages/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
			$("#company-available-page-list").html("");
	        $('#left-panel-tab .loading').show();
	    },
		success: function(json) {
			for(i in json){
				$("#company-available-page-list").append(
					"<li class='draggable ui-state-highlight'><input class='facebook_page_id' type='hidden' value='" + json[i].id + "'/><input class='page_name' type='hidden' value='" + json[i].name + "'/>" + json[i].name +"</li>"
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
	    url: base_url + "company/json_get_not_installed_apps/" + company_id + "/" + page_id,
	    dataType: "json",
	    beforeSend: function(){
			$("#page-available-app-list").html("");
	        $('#left-panel-tab .loading').show();
	    },
		success: function(json) {
            for(i in json){
				$("#page-available-app-list").append(
					"<li class='draggable ui-state-highlight'><input class='app_id' type='hidden' value='" + json[i].app_id + "'/><input class='app_secret_key' type='hidden' value='" + json[i].app_secret_key + "'/><input class='app_api_key' type='hidden' value='" + json[i].facebook_app_api_key + "'/>" + json[i].app_name +"</li>"
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
	//get all app install statuses
	$.getJSON(base_url + "app/json_get_all_app_install_status", function(json){
		for(i in json){
			all_app_install_statuses[''+json[i].app_install_status_name] = new Array(json[i].app_install_status_id,json[i].app_install_status_description);
		}
	});
	
	//get company detail
	$.getJSON(base_url + "company/json_get_profile/" + company_id, function(json){
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