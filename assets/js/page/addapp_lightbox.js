var sorted=true;
var all_app_install_statuses=new Array();
var dragging_object;
var available_pages=new Array();
//for app in page pagination
var installed_app_in_page_per_row=11;
var showing_page_of_installed_app_in_page=1;
var last_page_of_installed_app_in_page=1;
//
//for available item pagination
var available_item_per_page=9;
var showing_page_of_available_item=1;
var last_page_of_available_item=1;

function select_app_tab(){
	show_installed_app_in_page(page_id);
	show_available_app_in_page(page_id);
}
//add app to facebook page
function get_add_app_to_fb_page_link(app_facebook_api_key,facebook_page_id){
	return "http://www.facebook.com/add.php?api_key="+app_facebook_api_key+"&pages=1"+"&page="+facebook_page_id;
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
	for(var i=1;i<=last_page_of_available_item;i++) 
		strip_element.append('<li><a href="javascript:show_page(\'available_item\','+i+')"></a></li>');
	ul_element.children("li").hide();
	var k=(showing_page_of_available_item-1)*available_item_per_page;
	for(j=k;j<k+available_item_per_page;j++) ul_element.children("li").eq(j).show();
	strip_element.children('li').eq(showing_page_of_available_item-1).children('a').attr('class','current');
	//Remove pagination if there is one page
	if(strip_element.find("li").length == 1) {
		strip_element.find('li').remove();
	}
}

function refresh_installed_app_in_page_panel(){	
	var ul_element=$(".left-panel").find('.dragging-app div').find('ul');
	var strip_element=$(".left-panel").find('.strip ul');
	strip_element.html('');
	for(var i=1;i<=last_page_of_installed_app_in_page;i++) 
		strip_element.append('<li><a href="javascript:show_page(\'installed_app_in_page\','+i+')"></a></li>');
	ul_element.children("li").not(ul_element.children("li:first")).hide();
	var k=(showing_page_of_installed_app_in_page-1)*installed_app_in_page_per_row;
	for(j=k;j<k+installed_app_in_page_per_row;j++) ul_element.children("li").eq(j+1).show();
	strip_element.children('li').eq(showing_page_of_installed_app_in_page-1).children('a').attr('class','current');
	//Remove pagination if there is one page
	if(strip_element.find("li").length == 1) {
		strip_element.find('li').remove();
	}
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

//show installed apps in page
function show_installed_app_in_page(page_id,facebook_page_id){
	//get installed pages
	jQuery.ajax({
	    url: base_url + "page/json_get_installed_apps/" + page_id,
	    dataType: "json",
	    beforeSend: function(){
	    	$(".left-panel").find('.dragging-app div').html("<div class='loading'></div><ul></ul>");
	    	$(".head-dragging-app").hide();
	    },
		success: function(json) {
			var ul_element=$(".left-panel").find('.dragging-app div').find('ul');
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
								alert("ERROR! cannot install app.");
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
									});
									update_app_order_in_dashboard();
									$(".gotofacebook-link").live('click',function(){
										window.parent.location.replace(get_add_app_to_fb_page_link(app_api_key,facebook_page_id));
									});
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
			$(".head-dragging-app strong").html(json.length+' Applications installed in');
			$(".head-dragging-app b").html(page_name);
			$(".head-dragging-app").show();
	        $(".left-panel").find('.loading').remove();
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
					'<li class="draggable"><p><img src="'+imgsize(json[i].app_image,'normal')+'" alt="" width="64" height="64" /></p>'
					+'<p>'+ json[i].app_name +'</p>'
					+"<input class='app_id' type='hidden' value='" + json[i].app_id + "'/>"
					+"<input class='app_install_url' type='hidden' value='" + json[i].app_install_url + "'/>"		    
					+"<input class='app_secret_key' type='hidden' value='" + json[i].app_secret_key + "'/>"
					+"<input class='app_api_key' type='hidden' value='" + json[i].app_facebook_api_key + "'/></li>"
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
				},
				stop: function(){
					$(".left-panel").find('.dragging-app div').removeClass('in-action');
				}
	        });
	        $(".right-panel").find('.loading').remove();
        },
	});
}
$(function(){
	select_app_tab();
	//get all app install statuses
	$.getJSON(base_url + "app/json_get_all_app_install_status", function(json){
		for(i in json){
			all_app_install_statuses[''+json[i].app_install_status] = new Array(json[i].app_install_status_id,json[i].app_install_status_description);
		}
	});
	
	$( "ul, li" ).disableSelection();
});
