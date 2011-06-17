var sorted=true;
var all_app_install_statuses=new Array();
var dragging_object;
var adding_firstpage=false;
var adding_app=false;
var available_pages=new Array();
function select_page_tab(){
	$('li.page_tab').addClass("active");
	$('li.app_tab').removeClass("active");
	//get company pages count
	$.getJSON(base_url + "company/json_get_pages_count/" + company_id, function(json){
		$(".page-installed-count").html("Page (" + json.page_count + ")");
		if(json.page_count==0){
			$(".left-panel").html($(".no-installed-event-left").html());
			$(".right-panel").html($(".no-installed-event-right").html());
		}
		else{
			$(".left-panel").html($(".list-event-left").html());
			$(".right-panel").html($(".list-event-right").html());
			//get installed pages
			show_installed_page_in_company();
		}
	});
}
function select_app_tab(){
	$('li.app_tab').addClass("active");
	$('li.page_tab').removeClass("active");
	adding_app=true;
	adding_firstpage=false;
	$(".left-panel").html($(".dragging-event-left").html());
	$(".right-panel").html($(".dragging-event-right-app-list").html());
	show_installed_app_in_company();
	show_available_app_in_company();
}
function create_new_page_button_click(){
	$(".left-panel").html($(".dragging-event-left").html());
	$(".right-panel").html($(".dragging-event-right-page-list").html());
	adding_firstpage=true;
	adding_app=false;
}
function add_page_button_click(){
	$(".right-panel").html($(".dragging-event-right-page-list").html());
	adding_firstpage=false;
	adding_app=false;
}
function add_app_button_click(){
	$(".right-panel").html($(".dragging-event-right-app-list").html());
	adding_app=true;
	adding_firstpage=false;
}
//add app to facebook page
function get_add_app_to_fb_page_link(facebook_app_api_key,facebook_page_id){
	return "http://www.facebook.com/add.php?api_key="+facebook_app_api_key+"&pages=1"+"&page="+facebook_page_id;
}
//show apps in page
function view_page_app(page_id,facebook_page_id,page_name){
	show_installed_app_in_page(page_id,facebook_page_id);
	$(".head-box-app-list b").html(page_name);
	$(".right-panel").html($(".dragging-event-right-app-list").html());
	show_available_app_in_page(page_id);
	adding_firstpage=false;
	adding_app=true;
}

function view_page_app_nochange_right(page_id,facebook_page_id,page_name){
	show_installed_app_in_page(page_id,facebook_page_id);
	$(".head-box-app-list b").html(page_name);
	adding_firstpage=false;
	adding_app=false;
}
//show installed apps in company
function show_installed_app_in_company(){
	jQuery.ajax({
	    url: base_url + "company/json_get_installed_apps_not_in_page/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
			$(".left-panel").find('.dragging-page').html("<div class='loading'></div><ul></ul>");
	    },
		success: function(json) {
			var ul_element=$(".left-panel").find('.dragging-page').find('ul');
            for(i in json){
            	ul_element.append(
					'<li><p><img src="'+json[i].app_image+'" alt="" width="64" height="64" />'
					+'<span class="button">'
                    +'<a class="bt-update_app" href="'+base_url+'app/'+json[i].app_id+'"><span>Update</span></a>'
                    +'<a class="bt-setting_app" href="#"><span>Setting</span></a>'
                    +'</span>'
                    +'</p><p>'+json[i].app_name+'</p></li>'
                );
			}
			ul_element.droppable({
				drop: function(e, ui) {
					sorted=false;
					dragging_object=$(ui.draggable);
				},
				accept:"li.draggable"
			}).sortable({
				revert: true,
				stop: function(e,ui){
					if(!sorted){
						sorted=true;
						var app_id=dragging_object.children('input.app_id').val();
						var app_secret_key=dragging_object.children('input.app_secret_key').val();
						dragging_object.removeClass('draggable');
						jQuery.ajax({
							url: base_url + "app/json_add",
							dataType: "json",
							type: "POST",
							data: ({company_id : company_id, app_id : app_id, app_install_status : 1, page_id : 0 , app_install_secret_key : app_secret_key}),
							success: function(json) {
								if(json.status=="OK") alert("DONE");
								else alert("ERROR");
								show_available_app_in_company();		
								//update company installed apps count
								$.getJSON(base_url + "company/json_get_installed_apps_count/" + company_id, function(json){
									$(".app-installed-count").html("Application (" + json.app_count + ")");
								});
							},
						});
					}
				}
			});		
	        $(".left-panel").find('.loading').remove();
        },
	});
}

//show installed pages in company
function show_installed_page_in_company(){
	jQuery.ajax({
	    url: base_url + "company/json_get_pages/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
			$(".left-panel").find('.dragging-page div').html("<div class='loading'></div><ul></ul>");
	    },
		success: function(json) {
			var ul_element=$(".left-panel").find('.dragging-page div').find('ul');
			ul_element.append('<li class="add-page"></li>');
            for(i in json){
				if(adding_firstpage){
					ul_element.append(
						"<li><p><img src='"+json[i].page_image
						+"' alt='' width='80' height='80' /></p><p>"
						+json[i].page_name+"</p></li>"
					);
				}
				else{
					ul_element.append(
						'<li onclick="view_page_app('+json[i].page_id+','+json[i].facebook_page_id+',\''+json[i].page_name+'\')">'
						+'<p><img src="'+json[i].page_image+'" alt="" width="80" height="80" />'
						+'<span class="button">'
	                    +'<a class="bt-manage_page" href="'+base_url+'page/'+json[i].page_id+'"><span>Manage</span></a>'
	                    +'<a class="bt-setting_page" href="#"><span>Setting</span></a>'
	                    +'</span>'
	                    +'</p><p>'+json[i].page_name+'</p>'
						+'</li>'
					);
				}
			}
			if(json.length>0) view_page_app_nochange_right(json[0].page_id,json[0].facebook_page_id,json[0].page_name);
			ul_element.find('li').not('.drop-here,.add-page').bind('mouseover',function(){
				$(this).addClass("dragging");
			}).bind('mouseout',function(){
				$(this).removeClass("dragging");
			});
	        
			ul_element.droppable({
				drop: function(e, ui) {	
					sorted=false;
					dragging_object=$(ui.draggable);
				},
				accept:"li.draggable"
			}).sortable({
				cancel: ".add-page",
				placeholder: "ui-state-highlight",
				revert: true,
				stop: function(e,ui){
					if(!sorted){
						sorted=true;
						var facebook_page_id=dragging_object.children('input.facebook_page_id').val();
						var page_name=available_pages[''+facebook_page_id].name;
						var page_image=available_pages[''+facebook_page_id].page_info.picture;
						dragging_object.removeClass('draggable');
						jQuery.ajax({
							url: base_url + "page/json_add",
							dataType: "json",
							type: "POST",
							data: ({company_id : company_id, facebook_page_id : facebook_page_id, page_name : page_name, page_detail : "", page_all_member : 0, page_new_member : 0 , page_image : page_image}),
							success: function(json) {
								var page_id=json.page_id;
								var app_api_key=sh_default_fb_app_api_key;
								if(json.status=="OK"){
									$.getJSON(base_url + "app/json_get_app_by_api_key/" + app_api_key, function(app_info){
										jQuery.ajax({
											url: base_url + "app/json_add",
											dataType: "json",
											type: "POST",
											data: ({company_id : company_id, app_id : app_info.app_id, app_install_status : all_app_install_statuses['not complete install'][0], page_id : page_id , app_install_secret_key : app_info.app_secret_key}),
											success: function(json) {
												if(json.status=="OK"){
													alert("Go to Facebook to complete the action.");
												//	alert(get_add_app_to_fb_page_link(app_api_key,facebook_page_id));
													window.location=get_add_app_to_fb_page_link(app_api_key,facebook_page_id);
												}
												else alert("ERROR");
											},
										});
									});
									
								}
								else alert("ERROR");
								show_available_page_in_company();
								//update company pages count
								$.getJSON(base_url + "company/json_get_pages_count/" + company_id, function(json){
									$(".page-installed-count").html("Page (" + json.page_count + ")");
								});
							},
						});
					}
				}
			});		
	        $(".left-panel").find('.loading').remove();
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
	    	$(".left-panel").find('.dragging-app div').html("<div class='loading'></div><ul></ul>");
	    	$(".head-box-app-list").hide();
	    },
		success: function(json) {
			var ul_element=$(".left-panel").find('.dragging-app div').find('ul');
			ul_element.append('<li class="add-app"></li>');
            for(i in json){
				ul_element.append('<li><p><img src="'+json[i].app_image+'" alt="" width="64" height="64" />'
					+'<span class="button">'
                    +'<a class="bt-update_app" href="'+base_url+'app/'+json[i].app_id+'"><span>Update</span></a>'
                    +'<a class="bt-setting_app" href="#"><span>Setting</span></a>'
                    +'</span>'
                    +'</p><p>'+ json[i].app_name +'</p></li>');
				//"<li class='ui-state-default'>" + json[i].app_name + "<br/><a href='"+get_add_app_to_fb_page_link(json[i].facebook_app_api_key,facebook_page_id)+"'>Go to FB</a></li>"
			}
			ul_element.droppable({
				drop: function(e, ui) {
					sorted=false;
					dragging_object=$(ui.draggable);
				},
				accept:"li.draggable"
			}).sortable({
				cancel: '.add-app',
				placeholder: "ui-state-highlight",
				revert: true,
				stop: function(e,ui){
					if(!sorted){
						sorted=true;
						var app_id=dragging_object.children('input.app_id').val();
						var app_secret_key=dragging_object.children('input.app_secret_key').val();
						var app_api_key=dragging_object.children('input.app_api_key').val();
						dragging_object.removeClass('draggable');
						jQuery.ajax({
							url: base_url + "app/json_add",
							dataType: "json",
							type: "POST",
							data: ({company_id : company_id, app_id : app_id, app_install_status : all_app_install_statuses['not complete install'][0], page_id : page_id , app_install_secret_key : app_secret_key}),
							success: function(json) {
								if(json.status=="OK"){
									alert("Go to Facebook to complete the action.");
								//	alert(get_add_app_to_fb_page_link(app_api_key,facebook_page_id));
									window.location=get_add_app_to_fb_page_link(app_api_key,facebook_page_id);
								}
								else alert("ERROR");
								show_available_app_in_page(page_id);
								//update company installed apps count
								$.getJSON(base_url + "company/json_get_installed_apps_count/" + company_id, function(json){
									$(".app-installed-count").html("Application (" + json.app_count + ")");
								});
							},
						});
					}
				}
			});
			$(".add-app").click(function(){
				add_app_button_click();
				//get company available pages
				show_available_app_in_page(page_id);
			});
			$(".head-box-app-list strong").html(json.length+' Applications installed in');
			$(".head-box-app-list").show();
	        $(".left-panel").find('.loading').remove();
        },
	});
}

//show company's available apps
function show_available_app_in_company(){	
	jQuery.ajax({
	    url: base_url + "company/json_get_not_installed_apps/" + company_id + "/0",
	    dataType: "json",
	    beforeSend: function(){
			$(".right-panel").find('.box-app-list').html("<h2>Select Available Application</h2><div class='loading'></div><ul></ul>");
	   	},
		success: function(json) {
			var ul_element=$(".right-panel").find('.box-app-list').find('ul');
            for(i in json){
            	ul_element.append(
					'<li class="draggable"><p><img src="'+json[i].app_image+'" alt="" width="64" height="64" /></p>'
					+'<p>'+ json[i].app_name +'</p>'		    
					+"<input class='app_id' type='hidden' value='" + json[i].app_id + "'/>"
					+"<input class='app_secret_key' type='hidden' value='" + json[i].app_secret_key + "'/><input class='app_api_key' type='hidden' value='" + json[i].facebook_app_api_key + "'/></li>"
				);
			}
			ul_element.find('li.draggable').draggable({
	            connectToSortable: $(".left-panel").find('.dragging-page').find('ul'),
				helper: "clone",
				revert: "invalid"
	        });
	        $(".right-panel").find('.loading').remove();
        },
	});
}

//show company's available pages
function show_available_page_in_company(){
	jQuery.ajax({
	    url: base_url + "page/json_get_not_installed_facebook_pages/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
	        $(".right-panel").find('.dragging-page').html("<div class='loading'></div><ul></ul>");
	    },
		success: function(json) {
			var ul_element=$(".right-panel").find('.dragging-page').find('ul');
			available_pages=new Array();
			for(i in json){
				available_pages[''+json[i].id]=json[i];
				ul_element.append(
					"<li class='draggable'><p><img src='"
					+(json[i].page_info.picture==null?'http://profile.ak.fbcdn.net/static-ak/rsrc.php/v1/yA/r/gPCjrIGykBe.gif':json[i].page_info.picture)
					+"' alt='' width='80' height='80' /></p><p>"+json[i].name
					+"</p><input class='facebook_page_id' type='hidden' value='" + json[i].id + "'/></li>"
				);
			}
			ul_element.find('li').bind('mouseover',function(){
					$(this).addClass("dragging");
			}).bind('mouseout',function(){
					$(this).removeClass("dragging");
			});
			ul_element.find('li.draggable').draggable({
	            connectToSortable: $(".left-panel").find('.dragging-page').find('ul'),
				helper: "clone",
				revert: "invalid",
				drag: function(){
					$(".left-panel").find('.dragging-page div').addClass('in-action');
				},
				stop: function(){
					$(".left-panel").find('.dragging-page div').removeClass('in-action');
				}
	        });
	        $(".right-panel").find('.loading').remove();
        },
	});
}

//show page's available apps
function show_available_app_in_page(page_id){
	jQuery.ajax({
	    url: base_url + "company/json_get_not_installed_apps/" + company_id + "/" + page_id,
	    dataType: "json",
	    beforeSend: function(){
			$(".right-panel").find('.dragging-app').html("<div class='loading'></div><ul></ul>");
	   	},
		success: function(json) {
			var ul_element=$(".right-panel").find('.dragging-app').find('ul');
            for(i in json){
				ul_element.append(
					'<li class="draggable"><p><img src="'+json[i].app_image+'" alt="" width="64" height="64" /></p>'
					+'<p>'+ json[i].app_name +'</p>'		    
					+"<input class='app_id' type='hidden' value='" + json[i].app_id + "'/>"
					+"<input class='app_secret_key' type='hidden' value='" + json[i].app_secret_key + "'/><input class='app_api_key' type='hidden' value='" + json[i].facebook_app_api_key + "'/></li>"
				);
			}
			ul_element.find('li.draggable').draggable({
	            connectToSortable: $(".left-panel").find('.box-app-list').find('ul'),
				helper: "clone",
				revert: "invalid",
				drag: function(){
					$(".left-panel").find('.dragging-app div').addClass('in-action');
				},
				stop: function(){
					$(".left-panel").find('.dragging-app div').removeClass('in-action');
				}
	        });
	        $(".right-panel").find('.loading').remove();
        },
	});
}
$(function() {	
	$(".add-page").live('click',function(){
		add_page_button_click();
		//get company available pages
		show_available_page_in_company();
	})
	$(".bt-create_page").live('click',function(){
		create_new_page_button_click();
		//get company available pages
		show_available_page_in_company();
		//get installed pages
		show_installed_page_in_company();
	})
	select_page_tab();
	//get company installed apps count
	$.getJSON(base_url + "company/json_get_installed_apps_count/" + company_id, function(json){
		$(".app-installed-count").html("Application (" + json.app_count + ")");
	});
	//get all app install statuses
	$.getJSON(base_url + "app/json_get_all_app_install_status", function(json){
		for(i in json){
			all_app_install_statuses[''+json[i].app_install_status_name] = new Array(json[i].app_install_status_id,json[i].app_install_status_description);
		}
	});
	
	//get company detail
/*	$.getJSON(base_url + "company/json_get_profile/" + company_id, function(json){
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
	});*/
	
	$( "ul, li" ).disableSelection();
});