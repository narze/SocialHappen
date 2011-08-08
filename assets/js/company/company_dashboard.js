var sorted=true;
var all_app_install_statuses=new Array();
var dragging_object;
var available_pages=new Array();
//for page in company pagination
var installed_page_per_row=4;
var showing_page_of_installed_page=1;
var last_page_of_installed_page=1;
//for app in page pagination
var installed_app_in_page_per_row=11;
var showing_page_of_installed_app_in_page=1;
var last_page_of_installed_app_in_page=1;
//
//for available item pagination
var available_item_per_page=9;
var showing_page_of_available_item=1;
var last_page_of_available_item=1;
var selected_page_id=null;
function select_page_tab(){
	$('li.page_tab').addClass("active");
	$('li.app_tab').removeClass("active");
	//get company pages count
	$.getJSON(base_url + "company/json_get_pages_count/" + company_id, function(json){
		$(".page-installed-count").html("Page (" + json.page_count + ")");		
	    $("#info-installed-page").html('<span>Installed Page</span>'+json.page_count);
		$(".left-panel").html($(".page-tab-left").html());
		$(".right-panel").html($(".list-activity-log").html());
		get_activity_log();
		//get installed pages
		show_installed_page_in_company();
	});
}
function select_app_tab(){
	$('li.app_tab').addClass("active");
	$('li.page_tab').removeClass("active");
	$(".left-panel").html($(".app-tab-left").html());
	$(".right-panel").html($(".dragging-event-right-app-list").html());
	show_installed_app_in_company();
	show_available_app_in_company();
}
function create_new_page_button_click(){
	$(".left-panel").html($(".dragging-event-left").html());
	$(".right-panel").html($(".dragging-event-right-page-list").html());
}
function add_page_button_click(){
	$(".right-panel").html($(".dragging-event-right-page-list").html());
}
function add_app_button_click(){
	$(".right-panel").html($(".dragging-event-right-app-list").html());
}
//add app to facebook page
function get_add_app_to_fb_page_link(facebook_app_api_key,facebook_page_id){
	return "http://www.facebook.com/add.php?api_key="+facebook_app_api_key+"&pages=1"+"&page="+facebook_page_id;
}
//show apps in page
function view_page_app(page_id,facebook_page_id,page_name){
	selected_page_id=page_id;
	$(".left-panel").find('.dragging-page div').find('ul').children("li").removeClass("dragging").removeClass("active");
	$("li .page_id[value="+page_id+"]").parent("li").addClass("active");
	show_installed_app_in_page(page_id,facebook_page_id);
	$(".head-box-app-list b").html(page_name);
	$(".right-panel").html($(".dragging-event-right-app-list").html());
	show_available_app_in_page(page_id);
}

function view_page_app_nochange_right(page_id,facebook_page_id,page_name){
	selected_page_id=page_id;
	$(".left-panel").find('.dragging-page div').find('ul').children("li").removeClass("dragging").removeClass("active");
	$("li .page_id[value="+page_id+"]").parent("li").addClass("active");
	show_installed_app_in_page(page_id,facebook_page_id);
	$(".head-box-app-list b").html(page_name);
}

function update_page_order_in_dashboard(){	
	var i=0;
	var page_orders=new Array();
	$(".left-panel").find('.dragging-page div').find('ul li').each(function(){
		if(i>0){
			page_orders.push($(this).find('input.page_id').val());
		}
		i++;
	});
	$.post(base_url + "page/json_update_page_order_in_dashboard/" + company_id,{page_orders:page_orders}, function(json) {
	},"json");
}

function update_app_order_in_dashboard(){	
	var i=0;
	var app_orders=new Array();
	$(".left-panel").find('.dragging-app div').find('ul li').each(function(){
		if(i>0){
			app_orders.push($(this).find('input.app_install_id').val());
		}
		i++;
	});
	$.post(base_url + "app/json_update_app_order_in_dashboard",{app_orders:app_orders}, function(json) {
	},"json");
}
function refresh_available_item_panel(){
	if($(".right-panel").find('.dragging-app').size()>0)
		var ul_element=$(".right-panel").find('.dragging-app ul');
	else 
		var ul_element=$(".right-panel").find('.dragging-page ul');
	var strip_element=$(".right-panel").find('.strip ul');
	strip_element.html('');
	for(var i=1; i<=last_page_of_available_item && last_page_of_available_item != 1 ;i++) 
		strip_element.append('<li><a href="javascript:show_page(\'available_item\','+i+')"></a></li>');
	ul_element.children("li").hide();
	var k=(showing_page_of_available_item-1)*available_item_per_page;
	for(j=k;j<k+available_item_per_page;j++) ul_element.children("li").eq(j).show();
	strip_element.children('li').eq(showing_page_of_available_item-1).children('a').attr('class','current');
}
function refresh_installed_page_panel(){	
	var dragging_element=$(".left-panel").find('.dragging-page');
	var ul_element=$(".left-panel").find('.dragging-page div').find('ul');
	ul_element.children("li").not(ul_element.children("li:first")).hide();
	var k=(showing_page_of_installed_page-1)*installed_page_per_row;
	for(j=k;j<k+installed_page_per_row;j++) ul_element.children("li").eq(j+1).show();
	if(showing_page_of_installed_page==1){
		dragging_element.children('.back').removeClass('back').addClass('back-inactive');
	}
	else{	
		dragging_element.children('.back-inactive').removeClass('back-inactive').addClass('back');
	}
	if(showing_page_of_installed_page==last_page_of_installed_page){
		dragging_element.children('.next').removeClass('next').addClass('next-inactive');
	}
	else{	
		dragging_element.children('.next-inactive').removeClass('next-inactive').addClass('next');
	}
}

function refresh_installed_app_in_page_panel(){	
	var ul_element=$(".left-panel").find('.dragging-app div').find('ul');
	var strip_element=$(".left-panel").find('.strip ul');
	strip_element.html('');
	for(var i=1;i<=last_page_of_installed_app_in_page && last_page_of_installed_app_in_page != 1 ;i++) 
		strip_element.append('<li><a href="javascript:show_page(\'installed_app_in_page\','+i+')"></a></li>');
	ul_element.children("li").not(ul_element.children("li:first")).hide();
	var k=(showing_page_of_installed_app_in_page-1)*installed_app_in_page_per_row;
	for(j=k;j<k+installed_app_in_page_per_row;j++) ul_element.children("li").eq(j+1).show();
	strip_element.children('li').eq(showing_page_of_installed_app_in_page-1).children('a').attr('class','current');
}

function show_page(elementName,page){
	if(elementName=="installed_app_in_page"&&page>=1&&page<=last_page_of_installed_app_in_page){
		showing_page_of_installed_app_in_page=page;
		refresh_installed_app_in_page_panel();
	}
	else if(elementName=="available_item"&&page>=1&&page<=last_page_of_available_item){
		showing_page_of_available_item=page;
		refresh_available_item_panel();
	}
}

function next_page(elementName){
	if(elementName=="installed-page"&&showing_page_of_installed_page<last_page_of_installed_page){
		showing_page_of_installed_page++;
		refresh_installed_page_panel();
	}
}

function previous_page(elementName){
	if(elementName=="installed-page"&&showing_page_of_installed_page>1){	
		showing_page_of_installed_page--;
		refresh_installed_page_panel();
	}
}

//show installed pages in company
function show_installed_page_in_company(){
	jQuery.ajax({
	    url: base_url + "company/json_get_pages/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
			$(".left-panel").find('.dragging-page div').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
	    },
		success: function(json) {
			var ul_element=$(".left-panel").find('.dragging-page div').find('ul');
			ul_element.append('<li class="add-page"></li>');
            for(i in json){
            //	if(j%installed_page_per_row==0) ul_element.append('<div></div>');
			//	ul_element.children('div:last').append(
				ul_element.append(
					'<li style="display:none;" onclick="view_page_app('+json[i].page_id+','+json[i].facebook_page_id+',\''+json[i].page_name+'\')">'
					+'<p><img src="'+imgsize(json[i].page_image,'normal')+'" alt="" width="80" height="80" />'
					+'<span class="button">'
                    +'<a class="bt-manage_page" href="'+base_url+'page/'+json[i].page_id+'"><span>Manage</span></a>'
                    +'<a class="bt-setting_page" href="'+base_url+'settings/page/'+json[i].page_id+'"><span>Setting</span></a>'
                    +'</span>'
                    +'</p><p>'+json[i].page_name+'</p><input type="hidden" class="page_id" value="'+json[i].page_id+'" />'
					+'</li>'
				);
			}
			showing_page_of_installed_page=1;
			last_page_of_installed_page=Math.ceil(json.length/installed_page_per_row);
			refresh_installed_page_panel();		
			if(json.length>0) view_page_app_nochange_right(json[0].page_id,json[0].facebook_page_id,json[0].page_name);
			ul_element.find('li').not('.drop-here,.add-page').bind('mouseover',function(){
				$(this).addClass("dragging");
			}).bind('mouseout',function(){
				if(selected_page_id==null||$(this).children("input.page_id").val()!=selected_page_id)
					$(this).removeClass("dragging");
			});
	        
			ul_element.droppable({
				drop: function(e, ui) {	
					sorted=false;
					dragging_object=$(ui.draggable);
				},
				accept:"li.draggable"
			}).sortable({
				items: "li:not(.add-page)",
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
								dragging_object.append('<input type="hidden" value="'+page_id+'" class="page_id" />');
								dragging_object.children('p:first').append('<span class="button">'
				                    +'<a class="bt-manage_page" href="'+base_url+'page/'+page_id+'"><span>Manage</span></a>'
				                    +'<a class="bt-setting_page" href="'+base_url+'settings/page/'+page_id+'"><span>Setting</span></a>'
				                    +'</span>');
								dragging_object.attr('onclick',
									'view_page_app('+page_id+','+facebook_page_id+',\''+page_name+'\')');
								if(json!=null&&json.status.toUpperCase()=="OK"){									
									show_available_page_in_company();
									//update company pages count
									$.getJSON(base_url + "company/json_get_pages_count/" + company_id, function(json){
										$(".page-installed-count").html("Page (" + json.page_count + ")");
										$("#info-installed-page").html('<span>Installed Page</span>'+json.page_count);
									});
									update_page_order_in_dashboard();	
									var isSamePage=false;								
									var k=(showing_page_of_installed_page-1)*installed_page_per_row;
									for(j=k;j<k+installed_page_per_row;j++){
										if(ul_element.children("li").eq(j+1).children('.page_id').val()==page_id){
											isSamePage=true;
											break;
										}
									}
									if(!isSamePage) showing_page_of_installed_page++;
									refresh_installed_page_panel();
									$.getJSON(base_url + "app/json_get_app_by_api_key/" + app_api_key, function(app_info){
										app_install_url=app_info.app_install_url;
										app_install_url=app_install_url.replace("{company_id}",company_id)
													.replace("{user_id}",user_id)
													.replace("{page_id}",page_id)+"&force=1";										
										jQuery.ajax({
											url: base_url+"app/curl",
											dataType: "json",
											type: "POST",
											data: {url:app_install_url},
											error: function(){
												show_installed_app_in_page(page_id,facebook_page_id);
												show_available_app_in_page(page_id);
												alert("ERROR! cannot install app.");		
											},
											success: function(json) {
												if(json!=null&&json.status!=null&&json.status.toUpperCase()=="OK"){																						
													$("#gotofacebook-link").attr('onclick',
														'window.parent.location="'+get_add_app_to_fb_page_link(app_api_key,facebook_page_id)+'"');
													$.fancybox({
														content:$("#popup-gotofacebook").html()
													});
												}
												else{
													show_installed_app_in_page(page_id,facebook_page_id);
													show_available_app_in_page(page_id);
													alert("ERROR! cannot install app.");								
												}
											},
											complete: function(){
												view_page_app(page_id,facebook_page_id,page_name);
											},
										});
									});
									
								}
								else alert("ERROR");
							},
						});
					}
					else{
						update_page_order_in_dashboard();
					}
				}
			});		
	        $(".left-panel").find('.loading').remove();
        },
	});
}

//show installed apps in company
function show_installed_app_in_company(){
	jQuery.ajax({
	    url: base_url + "company/json_get_installed_apps_not_in_page/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
			$(".left-panel").find('.dragging-app div').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
	    },
		success: function(json) {
			var ul_element=$(".left-panel").find('.dragging-app div').find('ul');
			ul_element.append('<li class="add-app"></li>');
            for(i in json){
            	ul_element.append(
					'<li><p><img src="'+imgsize(json[i].app_image,'normal')+'" alt="" width="64" height="64" />'
					+'<span class="button">'
                    +'<a class="bt-update_app" href="'+base_url+'app/'+json[i].app_install_id+'"><span>Update</span></a>'
                    +'<a class="bt-setting_app" href="'+base_url+'settings/0/app/'+json[i].app_install_id+'"><span>Setting</span></a>'
                    +'</span>'
                    +'</p><p>'+json[i].app_name+'</p><input type="hidden" class="app_install_id" value="'+json[i].app_install_id+'" /></li>'
                );
			}		
			showing_page_of_installed_app_in_page=1;
			last_page_of_installed_app_in_page=Math.ceil(json.length/installed_app_in_page_per_row);
			refresh_installed_app_in_page_panel();	
			ul_element.droppable({
				drop: function(e, ui) {
					sorted=false;
					dragging_object=$(ui.draggable);
				},
				accept:"li.draggable"
			}).sortable({
				items: 'li:not(.add-app)',
				placeholder: "ui-state-highlight",
				revert: true,
				stop: function(e,ui){
					if(!sorted){
						sorted=true;
						var app_id=dragging_object.children('input.app_id').val();
						var app_secret_key=dragging_object.children('input.app_secret_key').val();
						var app_install_url=dragging_object.children('input.app_install_url').val();
						dragging_object.removeClass('draggable');
						app_install_url=app_install_url.replace("{company_id}",company_id)
									.replace("{user_id}",user_id)
									.replace("{page_id}",0)+"&force=1";										
						jQuery.ajax({
							url: base_url+"app/curl",
							dataType: "json",
							type: "POST",
							data: {url:app_install_url},
							error: function(){
								show_available_app_in_company();	
								show_installed_app_in_company();	
								alert("ERROR! cannot install app.");								
							},
							success: function(json) {
								if(json!=null&&json.status.toUpperCase()=="OK"){	
									app_install_id=json.app_install_id;
									dragging_object.append('<input type="hidden" value="'+app_install_id+'" class="app_install_id" />');
									dragging_object.children('p:first').append('<span class="button">'
				                    +'<a class="bt-update_app" href="'+base_url+'app/'+app_install_id+'"><span>Update</span></a>'
				                    +'<a class="bt-setting_app" href="'+base_url+'settings/0/app/'+json[i].app_install_id+'"><span>Setting</span></a>'
				                    +'</span>');
									update_app_order_in_dashboard();
									show_available_app_in_company();		
									//update company installed apps count
									$.getJSON(base_url + "company/json_get_installed_apps_count_not_in_page/" + company_id, function(json){
										$(".app-installed-count").html("Application (" + json.app_count + ")");
										$("#info-installed-app").html('<span>Installed Application</span>'+json.app_count);
									});
									alert("DONE");
								}
								else {
									show_available_app_in_company();	
									show_installed_app_in_company();	
									alert("ERROR! cannot install app.");								
								}
							}
						});
					}
					else{
						update_app_order_in_dashboard();
					}
				}
			});		
			$(".add-app").click(function(){
				add_app_button_click();
				//get company available pages
				show_available_app_in_company();
			});
			$(".head-dragging-app strong").html(json.length+' Applications installed');
			$(".head-dragging-app").show();
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
	    	$(".left-panel").find('.dragging-app div').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
	    	$(".head-box-app-list").hide();
	    },
		success: function(json) {
			var ul_element=$(".left-panel").find('.dragging-app div').find('ul');
			ul_element.append('<li class="add-app"></li>');
            for(i in json){
				ul_element.append('<li><p><img src="'+imgsize(json[i].app_image,'normal')+'" alt="" width="64" height="64" />'
					+'<span class="button">'
                    +'<a class="bt-update_app" href="'+base_url+'app/'+json[i].app_install_id+'"><span>Update</span></a>'
                    +'<a class="bt-setting_app" href="'+base_url+'settings/'+page_id+'/app/'+json[i].app_install_id+'"><span>Setting</span></a>'
                    +'</span>'
                    +'</p><p>'+ json[i].app_name +'</p><input type="hidden" class="app_install_id" value="'+json[i].app_install_id+'" /></li>');
			}			
			showing_page_of_installed_app_in_page=1;
			last_page_of_installed_app_in_page=Math.ceil(json.length/installed_app_in_page_per_row);
			refresh_installed_app_in_page_panel();		
			ul_element.droppable({
				drop: function(e, ui) {
					sorted=false;
					dragging_object=$(ui.draggable);
				},
				accept:"li.draggable"
			}).sortable({
				items: 'li:not(.add-app)',
				placeholder: "ui-state-highlight",
				revert: true,
				stop: function(e,ui){
					if(!sorted){
						sorted=true;
						var app_id=dragging_object.children('input.app_id').val();
						var app_secret_key=dragging_object.children('input.app_secret_key').val();
						var app_api_key=dragging_object.children('input.app_api_key').val();
						var app_install_url=dragging_object.children('input.app_install_url').val();
						dragging_object.removeClass('draggable');
						
						app_install_url=app_install_url.replace("{company_id}",company_id)
										.replace("{user_id}",user_id)
										.replace("{page_id}",page_id)+"&force=1";
									
						jQuery.ajax({
							url: base_url+"app/curl",
							dataType: "json",
							type: "POST",
							data: {url:app_install_url},
							error: function(){
								show_installed_app_in_page(page_id,facebook_page_id);
								show_available_app_in_page(page_id);
								alert("ERROR");								
							},
							success: function(json) {
								if(json!=null&&json.status!=null&&json.status.toUpperCase()=="OK"){
									app_install_id=json.app_install_id;
									dragging_object.append('<input type="hidden" value="'+app_install_id+'" class="app_install_id" />');
									dragging_object.children('p:first').append('<span class="button">'
				                    +'<a class="bt-update_app" href="'+base_url+'app/'+app_install_id+'"><span>Update</span></a>'
				                    +'<a class="bt-setting_app" href="'+base_url+'settings/'+page_id+'/app/'+app_install_id+'"><span>Setting</span></a>'
				                    +'</span>');
									refresh_installed_app_in_page_panel();
									show_available_app_in_page(page_id);
									//update company installed apps count
									$.getJSON(base_url + "company/json_get_installed_apps_count_not_in_page/" + company_id, function(json){
										$(".app-installed-count").html("Application (" + json.app_count + ")");
										$("#info-installed-app").html('<span>Installed Application</span>'+json.app_count);
									});
									update_app_order_in_dashboard();									
									$("#gotofacebook-link").attr('onclick',
										'window.parent.location="'+get_add_app_to_fb_page_link(app_api_key,facebook_page_id)+'"');
									$.fancybox({
										content:$("#popup-gotofacebook").html()
									});
								}
								else{
									show_installed_app_in_page(page_id,facebook_page_id);
									show_available_app_in_page(page_id);
									alert("ERROR");
								}
							},
						});
					}
					else{
						update_app_order_in_dashboard();
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

//show company's available pages
function show_available_page_in_company(){
	jQuery.ajax({
	    url: base_url + "page/json_get_not_installed_facebook_pages/" + company_id,
	    dataType: "json",
	    beforeSend: function(){
	        $(".right-panel").find('.dragging-page').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
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
			showing_page_of_available_item=1;
			available_item_per_page=9;
			last_page_of_available_item=Math.ceil(json.length/available_item_per_page);
			refresh_available_item_panel();	
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
					$("div.dragging-page ul").css('height','auto');
				},
				stop: function(){
					$(".left-panel").find('.dragging-page div').removeClass('in-action');
					$("div.dragging-page ul").css('height','155px');
				}
	        });
	        $(".right-panel").find('.loading').remove();
        },
	});
}

//show company's available apps
function show_available_app_in_company(){
	jQuery.ajax({
	    url: base_url + "company/json_get_not_installed_apps/" + company_id + "/0",
	    dataType: "json",
	    beforeSend: function(){
			$(".right-panel").find('.dragging-app').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
	   	},
		success: function(json) {
			var ul_element=$(".right-panel").find('.dragging-app').find('ul');
            for(i in json){
            	ul_element.append(
					'<li class="draggable"><p><img src="'+imgsize(json[i].app_image,'normal')+'" alt="" width="64" height="64" /></p>'
					+'<p>'+ json[i].app_name +'</p>'		    
					+"<input class='app_id' type='hidden' value='" + json[i].app_id + "'/>"
					+"<input class='app_install_url' type='hidden' value='" + json[i].app_install_url + "'/>"
					+"<input class='app_secret_key' type='hidden' value='" + json[i].app_secret_key + "'/>"
					+"<input class='app_api_key' type='hidden' value='" + json[i].facebook_app_api_key + "'/></li>"
				);
			}
			showing_page_of_available_item=1;
			available_item_per_page=9;
			last_page_of_available_item=Math.ceil(json.length/available_item_per_page);
			refresh_available_item_panel();	
			ul_element.find('li.draggable').draggable({
	            connectToSortable: $(".left-panel").find('.dragging-app div').find('ul'),
				helper: "clone",
				revert: "invalid",
				drag: function(){
					$(".left-panel").find('.dragging-app div').addClass('in-action');					
					$("div.dragging-app ul").css('height','auto');
				},
				stop: function(){
					$(".left-panel").find('.dragging-app div').removeClass('in-action');					
					$("div.dragging-app ul").css('height','255px');
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
			$(".right-panel").find('.dragging-app').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
	   	},
		success: function(json) {
			var ul_element=$(".right-panel").find('.dragging-app').find('ul');
            for(i in json){
				ul_element.append(
					'<li class="draggable"><p><img src="'+imgsize(json[i].app_image,'normal')+'" alt="" width="64" height="64" /></p>'
					+'<p>'+ json[i].app_name +'</p>'
					+"<input class='app_id' type='hidden' value='" + json[i].app_id + "'/>"
					+"<input class='app_install_url' type='hidden' value='" + json[i].app_install_url + "'/>"		    
					+"<input class='app_secret_key' type='hidden' value='" + json[i].app_secret_key + "'/>"
					+"<input class='app_api_key' type='hidden' value='" + json[i].facebook_app_api_key + "'/></li>"
				);
			}
			showing_page_of_available_item=1;
			available_item_per_page=9;
			last_page_of_available_item=Math.ceil(json.length/available_item_per_page);
			refresh_available_item_panel();	
			ul_element.find('li.draggable').draggable({
	            connectToSortable: $(".left-panel").find('.box-app-list').find('ul'),
				helper: "clone",
				revert: "invalid",
				drag: function(){
					$(".left-panel").find('.dragging-app div').addClass('in-action');					
					$("div.dragging-app ul").css('height','auto');					
				},
				stop: function(){
					$(".left-panel").find('.dragging-app div').removeClass('in-action');					
					$("div.dragging-app ul").css('height','255px');
				}
	        });
	        $(".right-panel").find('.loading').remove();
        },
	});
}

function get_activity_log(){	
	$('.activity-logs ul').html('');
	jQuery.ajax({
	    url: base_url + "audit/json_get_company_activity_log/" + company_id,
	    dataType: "json",
	    beforeSend: function(){},
		success: function(json) {
			for(i in json){
				var action_desc=""
				if(json[i].action_id==1||json[i].action_id==2) action_desc="add an application";
				else if(json[i].action_id==5) action_desc="create a new page";
				var appendingHTML=
					'<li><p><strong>'+
					'<a href="'+base_url+'user/'+json[i].user_id+'">'+json[i].user_first_name+'</a>'+
					'</strong> '+action_desc;				
				if(json[i].action_id==1||json[i].action_id==2)
					appendingHTML+=' <a href="'+base_url+'app/'+json[i].app_install_id+'">'+json[i].app_name+'</a>';
				else if(json[i].action_id==5)
					appendingHTML+=' <a href="'+base_url+'page/'+json[i].page_id+'">'+json[i].page_name+'</a>';
				if(json[i].action_id==2) appendingHTML+=' in <a href="'+base_url+'page/'+json[i].page_id+'">'+json[i].page_name+'</a>';
				appendingHTML+=
					'</p>'+
					'<p class="thumb">'+
						'<img src="'+json[i].image+'" alt="" />'+
					'</p>'+
					'<p>'+
						'<span>'+json[i].datetime+'</span>'+
					'</p></li>';
				$('.activity-logs ul').append(appendingHTML);
			}
		}
	});
}
$(function() {
	get_activity_log();
	$(".add-page").live('click',function(){
		add_page_button_click();
		//get company available pages
		show_available_page_in_company();
	});
	$(".bt-create_page").live('click',function(){
		create_new_page_button_click();
		//get company available pages
		show_available_page_in_company();
		//get installed pages
		show_installed_page_in_company();
	});
	select_page_tab();
	//get company installed apps count
	$.getJSON(base_url + "company/json_get_installed_apps_count_not_in_page/" + company_id, function(json){
		$(".app-installed-count").html("Application (" + json.app_count + ")");
		$("#info-installed-app").html('<span>Installed Application</span>'+json.app_count);
	});
	//get all app install statuses
	$.getJSON(base_url + "app/json_get_all_app_install_status", function(json){
		for(i in json){
			all_app_install_statuses[''+json[i].app_install_status_name] = new Array(json[i].app_install_status_id,json[i].app_install_status_description);
		}
	});
	
	$( "ul, li" ).disableSelection();
});