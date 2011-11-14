(function( window, undefined ) {
	var shDragging = (function() {
		var mode = 'company';
		var shDragging = {};
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

		//Page + App status
		var page_installed=0;
		var app_install_status=0;
		
		shDragging = shDragging.prototype = {
			company_mode: function(){
				mode = 'company';
			},
			page_mode: function(){
				mode = 'page';
			},
			mode:function(){return mode;},
			select_page_tab: function() {
				if(mode == 'company'){
					$('li.page_tab').addClass("active");
					$('li.app_tab').removeClass("active");
					//get company pages count
					$.getJSON(base_url + "company/json_get_pages_count/" + company_id, function(json) {
						$(".page-installed-count").html("Page (" + json.page_count + ")");
						$("#info-installed-page").html('<span>Installed Page</span>'+json.page_count);
						$(".left-panel").html($(".page-tab-left").html());
						$(".right-panel").html($(".list-activity-log").html());
						shDragging.get_activity_log();
						//get installed pages
						shDragging.show_installed_page_in_company();
					});
				}
			},
			create_new_page_button_click : function() {
				if(mode == 'company'){
					$(".left-panel").html($(".dragging-event-left").html());
					$(".right-panel").html($(".dragging-event-right-page-list").html());
				}
			},
			add_page_button_click : function () {
				if(mode == 'company'){
					$(".right-panel").html($(".dragging-event-right-page-list").html());
				}
			},
			add_app_button_click : function() {
				if(mode == 'company'){
					$(".right-panel").html($(".dragging-event-right-app-list").html());
				}
			},
			get_page_installed : function(page_id) {
				if(mode == 'company'){
					$.ajax({
						async:false,
						url: base_url + "page/json_get_profile/" + page_id,
						dataType: "json",
						success: function (page) {
							page_installed = page.page_installed;
						}
					});
					return page_installed;
				}
			},
			view_page_app : function(page_id,facebook_page_id,page_name) {
				if(mode == 'company'){
					$(".left-panel").find('.dragging-page div').find('ul').children("li").removeClass("dragging").removeClass("active");
					$("li .page_id[value="+page_id+"]").parent("li").addClass("active");
					$('div.dragging-app').show();
					
					selected_page_id=page_id;
					shDragging.show_installed_app_in_page(page_id,facebook_page_id);
					$(".head-box-app-list b").html(page_name);
					
					var right_panel = $('.right-panel');
					if(page_installed == 0) //Page installation not complete
					{
						right_panel.find('.dragging-app').empty();
						right_panel.find('p.alert').remove();
						return false;
					}
					
					right_panel.html($(".dragging-event-right-app-list").html());
					shDragging.show_available_app_in_page(page_id);
				}
			},
			view_page_app_nochange_right : function(page_id,facebook_page_id,page_name) {
				if(mode == 'company'){
					selected_page_id=page_id;
					$(".left-panel").find('.dragging-page div').find('ul').children("li").removeClass("dragging").removeClass("active");
					$("li .page_id[value="+page_id+"]").parent("li").addClass("active");
					shDragging.show_installed_app_in_page(page_id,facebook_page_id);
					$(".head-box-app-list b").html(page_name);
				}
			},
			update_page_order_in_dashboard : function() {
				if(mode == 'company'){
					var i=0;
					var page_orders=new Array();
					$(".left-panel").find('.dragging-page div').find('ul li').each( function() {
						if(i>0) {
							page_orders.push($(this).find('input.page_id').val());
						}
						i++;
					});
					$.post(base_url + "page/json_update_page_order_in_dashboard/" + company_id, {
						page_orders:page_orders
					}, function(json) {
					},"json");
				}
			},
			refresh_installed_page_panel : function() {
				if(mode == 'company'){
					var dragging_element=$(".left-panel").find('.dragging-page');
					var ul_element=$(".left-panel").find('.dragging-page div').find('ul');
					ul_element.children("li").not(ul_element.children("li:first")).hide();
					var k=(showing_page_of_installed_page-1)*installed_page_per_row;
					for(j=k;j<k+installed_page_per_row;j++)
						ul_element.children("li").eq(j+1).show();
					if(showing_page_of_installed_page==1) {
						dragging_element.children('.back').removeClass('back').addClass('back-inactive');
					} else {
						dragging_element.children('.back-inactive').removeClass('back-inactive').addClass('back');
					}
					if(showing_page_of_installed_page==last_page_of_installed_page) {
						dragging_element.children('.next').removeClass('next').addClass('next-inactive');
					} else {
						dragging_element.children('.next-inactive').removeClass('next-inactive').addClass('next');
					}
				}
			},
			next_page : function(elementName) {
				if(mode == 'company'){
					if(elementName=="installed-page"&&showing_page_of_installed_page<last_page_of_installed_page) {
						showing_page_of_installed_page++;
						shDragging.refresh_installed_page_panel();
					}
				}
			},
			previous_page : function(elementName) {
				if(mode == 'company'){
					if(elementName=="installed-page"&&showing_page_of_installed_page>1) {
						showing_page_of_installed_page--;
						shDragging.refresh_installed_page_panel();
					}
				}
			},
			crop_page_image : function(element) {
				element.find('img').load(function () {
					var cpyImg = new Image();
					cpyImg.src = element.find('img').attr('src');
					if(cpyImg.width > cpyImg.height) { 
						var imgCurrentWidth =  Math.floor(cpyImg.width * 80 / cpyImg.height);
						element.find('img').css({
							'width' : 'auto',
							'position' : 'relative',
							'left' : '-' + ((imgCurrentWidth/2)-40) + 'px'//Center img
						}); 
					}
					else if(cpyImg.width < cpyImg.height) { 
						element.find('img').css('height', 'auto');
					}
				});
				return element;
			},
			show_installed_page_in_company : function() {
				if(mode == 'company'){
					jQuery.ajax({
						async:true,
						url: base_url + "company/json_get_pages/" + company_id,
						dataType: "json",
						beforeSend: function() {
							$(".left-panel").find('.dragging-page div').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
						},
						success: function(json) {
							var ul_element=$(".left-panel").find('.dragging-page div').find('ul').css('max-height', '155px').css('overflow', 'hidden');
							ul_element.append('<li class="add-page"></li>');
							for(i in json) {
								//	if(j%installed_page_per_row==0) ul_element.append('<div></div>');
								//	ul_element.children('div:last').append(
								var li = $('<li style="display:none;" onclick="shDragging.view_page_app('+json[i].page_id+','+json[i].facebook_page_id+',\''+json[i].page_name+'\')">'
								+'<p class="page-image"><img src="'+imgsize(json[i].page_image,'normal')+'" alt="" />'
								+'<span class="button">'
								+'<a class="bt-manage_page" href="'+base_url+'page/'+json[i].page_id+'"><span>Manage</span></a>'
								+'<a class="bt-setting_page" href="'+base_url+'settings/page/'+json[i].page_id+'"><span>Setting</span></a>'
								+'</span>'
								+'</p><p class="pagename">'+json[i].page_name+'</p><input type="hidden" class="page_id" value="'+json[i].page_id+'" />'
								+'</li>');
								
								li = shDragging.crop_page_image(li);
								ul_element.append(li);
							}
							showing_page_of_installed_page=1;
							last_page_of_installed_page=Math.ceil(json.length/installed_page_per_row);
							shDragging.refresh_installed_page_panel();
							if(json.length>0)
								shDragging.view_page_app_nochange_right(json[0].page_id,json[0].facebook_page_id,json[0].page_name);
							ul_element.find('li').not('.drop-here,.add-page').bind('mouseover', function() {
								$(this).addClass("dragging");
							}).bind('mouseout', function() {
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
								stop: function(e,ui) {
									if(!sorted) {
										sorted=true;
										var facebook_page_id=dragging_object.children('input.facebook_page_id').val();
										var page_name=available_pages[''+facebook_page_id].name;
										var page_image=available_pages[''+facebook_page_id].page_info.picture;
										dragging_object.removeClass('draggable');
										jQuery.ajax({
											async:true,
											url: base_url + "page/json_add",
											dataType: "json",
											type: "POST",
											data: ( {
												company_id : company_id,
												facebook_page_id : facebook_page_id,
												page_name : page_name,
												page_detail : "",
												page_all_member : 0,
												page_new_member : 0 ,
												page_image : page_image
											}),
											success: function(json) {
												var page_id=json.page_id;
												var app_api_key=sh_default_fb_app_api_key;
												if(json!=null&&json.status!=null&&json.status.toUpperCase()=="OK") {
													$.fancybox({
														href: base_url+'company/page_installed',
														onComplete: function () { 
															$('#popup-goto-facebook').find(".bt-go-facebook").attr('href', json.facebook_tab_url); 
														}
													});
													$(dragging_object).attr('onclick','shDragging.view_page_app('+page_id+','+facebook_page_id+',"'+page_name+'")');
													$(dragging_object).append('<input type="hidden" class="page_id" value="'+ page_id +'">');
													$('.right-panel .dragging-page ul input.facebook_page_id[value="'+facebook_page_id+'"]').parents('li').remove();
													
												} else {
													// show_installed_app_in_page(page_id,facebook_page_id); //no page_id
													// show_available_app_in_page(page_id);
													//console.log(json);
													$(dragging_object).remove();
													$.fancybox({
														content: 'This page is already installed by another company.'
													});
												}
											},
										});
									} else {
										shDragging.update_page_order_in_dashboard();
									}
								}
							});
							$(".left-panel").find('.loading').remove();
						},
					});
				}
			},
			show_installed_app_in_company : function() {
				if(mode == 'company'){
					var left_panel = $(".left-panel");
					jQuery.ajax({
						async:true,
						url: base_url + "company/json_get_installed_apps_not_in_page/" + company_id,
						dataType: "json",
						beforeSend: function() {
							left_panel.find('.dragging-app div').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
						},
						success: function(json) {
							var ul_element=left_panel.find('.dragging-app div').find('ul');
							ul_element.append('<li class="add-app"></li>');
							for(i in json) {
								ul_element.append(
								'<li><p><img class="app-image" src="'+imgsize(json[i].app_image,'normal')+'" alt="" width="64" height="64" />'
								+'<span class="button">'
								+'<a class="bt-setting_app" href="'+base_url+'app/config/'+json[i].app_install_id+'"><span>Setting</span></a>'
								+'</span>'
								+'</p><p class="appname">'+json[i].app_name+'</p><input type="hidden" class="app_install_id" value="'+json[i].app_install_id+'" /></li>'
								);
							}
							showing_page_of_installed_app_in_page=1;
							last_page_of_installed_app_in_page=Math.ceil(json.length/installed_app_in_page_per_row);
							shDragging.refresh_installed_app_in_page_panel();
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
								stop: function(e,ui) {
									if(!sorted) {
										sorted=true;
										var app_id=dragging_object.children('input.app_id').val();
										var app_secret_key=dragging_object.children('input.app_secret_key').val();
										var app_install_url=dragging_object.children('input.app_install_url').val();
										dragging_object.removeClass('draggable');
										app_install_url=app_install_url.replace("{company_id}",company_id)
										.replace("{user_id}",user_id)
										.replace("{page_id}",0)+"&force=1";
										jQuery.ajax({
											async:true,
											url: base_url+"app/json_add",
											dataType: "json",
											type: "POST",
											data: {
												url:app_install_url
											},
											error: function() {
												shDragging.show_available_app_in_company();
												shDragging.show_installed_app_in_company();
												$.fancybox({
													content: 'Cannot install app, please contact administrator.'
												});
												//console.log("app/curl failed 2");
											},
											success: function(json) {
												if(json!=null&&json.status.toUpperCase()=="OK") {
													var app_install_id=json.app_install_id;
													dragging_object.append('<input type="hidden" value="'+app_install_id+'" class="app_install_id" />');
													dragging_object.children('p:first').append('<span class="button">'
													+'<a class="bt-setting_app" href="'+base_url+'app/config/'+app_install_id+'"><span>Setting</span></a>'
													+'</span>');
													shDragging.update_app_order_in_dashboard();
													shDragging.show_available_app_in_company();
													//update company installed apps count
													$.getJSON(base_url + "company/json_get_installed_apps_count_not_in_page/" + company_id, function(json) {
														$(".app-installed-count").html("Application (" + json.app_count + ")");
														$("#info-installed-app").html('<span>Installed Application</span>'+json.app_count);
													});
												} else {
													shDragging.show_available_app_in_company();
													shDragging.show_installed_app_in_company();
													$.fancybox({
													content: 'Cannot install app, please contact administrator.'
												});
													//console.log("app/curl json mismatch 2 : " + json);
												}
											}
										});
									} else {
										shDragging.update_app_order_in_dashboard();
									}
								}
							});
							$(".add-app").click( function() {
								shDragging.add_app_button_click();
								//get company available pages
								shDragging.show_available_app_in_company();
							});
							$(".head-dragging-app strong").html(json.length+' Applications installed');
							$(".head-dragging-app").show();
							left_panel.find('.loading').remove();
						},
					});
				}
			},
			show_available_page_in_company : function() {
				var left_panel = $(".left-panel");
				if(mode == 'company'){
					FB.api(
					  {
						method: 'fql.query',
						query: "SELECT page_id, name, has_added_app, pic from page WHERE page_id IN (SELECT page_id from page_admin WHERE uid=me())"
					  },
					  function(page_pics) {
						json_page_pics = JSON.stringify(page_pics);
						jQuery.ajax({
							type:'POST',
							data:{page_pics: json_page_pics},
							async:true,
							url: base_url + "page/json_get_not_installed_facebook_pages/" + company_id,
							dataType: "json",
							beforeSend: function() {
								$(".right-panel").find('.dragging-page').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
							},
							success: function(json) {
								var ul_element=$(".right-panel").disableSelection().find('.dragging-page').find('ul').empty();
								available_pages=new Array();
								for(i in json) {
									available_pages[''+json[i].id]=json[i];
									// "<li class='draggable'><p><img src='"
									// +(json[i].page_info.picture==null?'http://profile.ak.fbcdn.net/static-ak/rsrc.php/v1/yA/r/gPCjrIGykBe.gif':json[i].page_info.picture)
									var li = $("<li data-hasaddedapp='"+json[i].has_added_app+"' class='draggable'><p class='page-image'><img src='"
									+json[i].page_info.picture
									+"' alt='' /></p><p class='pagename'>"+json[i].name
									+"</p><input class='facebook_page_id' type='hidden' value='" + json[i].id + "'/></li>");

									li = shDragging.crop_page_image(li);
									ul_element.append(li);
								}
								showing_page_of_available_item=1;
								available_item_per_page=9;
								last_page_of_available_item=Math.ceil(json.length/available_item_per_page);
								shDragging.refresh_available_item_panel();
								ul_element.find('li[data-hasaddedapp="false"]').bind('mouseover', function() {
									$(this).addClass("dragging");
								}).bind('mouseout', function() {
									$(this).removeClass("dragging");
								});
								ul_element.find('li[data-hasaddedapp="true"] img').after('<span class=\"button\"><a class=\"bt-installed-app\">Installed</a></span>');
								// //for real use : don't allow re-install
								// ul_element.find('li.draggable[data-hasaddedapp="false"]').draggable({ 
									// connectToSortable: left_panel.find('.dragging-page').find('ul'),
									// helper: "clone",
									// revert: "invalid",
									// drag: function() {
										// left_panel.find('.dragging-page div').addClass('in-action');
										// $("div.dragging-page ul").css('height','auto');
									// },
									// stop: function() {
										// left_panel.find('.dragging-page div').removeClass('in-action');
										// $("div.dragging-page ul").css('height','155px');
									// }
								// });
								// ul_element.find('li.draggable[data-hasaddedapp="true"]').die().live('click',function(){
									// $.fancybox({
										// content: 'This page has already been added by other user. If this page is yours, please remove Socialhappen Application from this page first'
									// });
								// });
								//for debug
								ul_element.find('li.draggable').draggable({ 
									connectToSortable: left_panel.find('.dragging-page').find('ul'),
									helper: "clone",
									revert: "invalid",
									drag: function() {
										left_panel.find('.dragging-page div').addClass('in-action');
										$("div.dragging-page ul").css('height','auto');
									},
									stop: function() {
										left_panel.find('.dragging-page div').removeClass('in-action');
										$("div.dragging-page ul").css('height','155px');
									}
								});
								//end for debug
								$(".right-panel").find('.loading').remove();
							},
						});
					  }
					);
				}
			},
			show_available_app_in_company : function() {
				var left_panel = $(".left-panel");
				if(mode == 'company'){
					jQuery.ajax({
						async:true,
						url: base_url + "company/json_get_not_installed_apps/" + company_id + "/0",
						dataType: "json",
						beforeSend: function() {
							$(".right-panel").find('.dragging-app').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
						},
						success: function(json) {
							var ul_element=$(".right-panel").find('.dragging-app').find('ul').empty();
							for(i in json) {
								if(json[i].app_type == 'Page Support' || json[i].app_type == 'Standalone'){
									ul_element.append(
									'<li class="draggable"><p><img class="app-image" src="'+imgsize(json[i].app_image,'normal')+'" alt="" width="64" height="64" /></p>'
									+'<p class="appname">'+ json[i].app_name +'</p>'
									+"<input class='app_id' type='hidden' value='" + json[i].app_id + "'/>"
									+"<input class='app_install_url' type='hidden' value='" + json[i].app_install_url + "'/>"
									+"<input class='app_secret_key' type='hidden' value='" + json[i].app_secret_key + "'/>"
									+"<input class='app_api_key' type='hidden' value='" + json[i].app_facebook_api_key + "'/></li>"
									);
								}
							}
							showing_page_of_available_item=1;
							available_item_per_page=9;
							last_page_of_available_item=Math.ceil(json.length/available_item_per_page);
							shDragging.refresh_available_item_panel();
							ul_element.find('li.draggable').draggable({
								connectToSortable: left_panel.find('.dragging-app div').find('ul'),
								helper: "clone",
								revert: "invalid",
								drag: function() {
									left_panel.find('.dragging-app div').addClass('in-action');
									$("div.dragging-app ul").css('height','auto');
								},
								stop: function() {
									left_panel.find('.dragging-app div').removeClass('in-action');
									$("div.dragging-app ul").css('height','255px');
								}
							});
							$(".right-panel").find('.loading').remove();
						},
					});
				}
			},
			get_activity_log : function() {
				if(mode == 'company'){
					$('.activity-logs ul').html('');
					jQuery.ajax({
						async:true,
						url: base_url + "audit/json_get_company_activity_log/" + company_id,
						dataType: "json",
						beforeSend: function() {
						},
						success: function(json) {
							for(i in json) {
								var action_desc=""
								if(json[i].action_id==1||json[i].action_id==2)
									action_desc="add an application";
								else if(json[i].action_id==5)
									action_desc="create a new page";
								var appendingHTML=
								'<li><p><strong>'+
								'<a href="'+base_url+'user/'+json[i].user_id+'">'+json[i].user_first_name+'</a>'+
								'</strong> '+action_desc;
								if(json[i].action_id==1||json[i].action_id==2)
									appendingHTML+=' <a href="'+base_url+'app/'+json[i].app_install_id+'">'+json[i].app_name+'</a>';
								else if(json[i].action_id==5)
									appendingHTML+=' <a href="'+base_url+'page/'+json[i].page_id+'">'+json[i].page_name+'</a>';
								if(json[i].action_id==2)
									appendingHTML+=' in <a href="'+base_url+'page/'+json[i].page_id+'">'+json[i].page_name+'</a>';
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
			},
			select_app_tab : function(){
				if(mode == 'company'){
					$('li.app_tab').addClass("active");
					$('li.page_tab').removeClass("active");
					$(".left-panel").html($(".app-tab-left").html());
					$(".right-panel").html($(".dragging-event-right-app-list").html());
					shDragging.show_installed_app_in_company();
					shDragging.show_available_app_in_company();
				} else if (mode == 'page'){
					shDragging.show_installed_app_in_page(page_id, facebook_page_id);
					shDragging.show_available_app_in_page(page_id);
				}
			},
			update_app_order_in_dashboard : function() {
				var i=0;
				var app_orders=new Array();
				$(".left-panel").find('.dragging-app div').find('ul li').each( function() {
					if(i>0) {
						app_orders.push($(this).find('input.app_install_id').val());
					}
					i++;
				});
				$.post(base_url + "app/json_update_app_order_in_dashboard", {
					app_orders:app_orders
				}, function(json) {
				},"json");
			},
			show_page : function(elementName,page) {
				if(elementName=="installed_app_in_page"&&page>=1&&page<=last_page_of_installed_app_in_page) {
					showing_page_of_installed_app_in_page=page;
					shDragging.refresh_installed_app_in_page_panel();
				} else if(elementName=="available_item"&&page>=1&&page<=last_page_of_available_item) {
					showing_page_of_available_item=page;
					shDragging.refresh_available_item_panel();
				}
			},
			
			refresh_available_item_panel : function() {
				var right_panel = $(".right-panel");
				var ul_element;
				if(right_panel.find('.dragging-app').size()>0){
					ul_element=right_panel.find('.dragging-app ul');
				} else {
					ul_element=right_panel.find('.dragging-page ul');
				}
				var strip_element=right_panel.find('.strip ul').empty();
			
				if(mode == 'company'){
					for(var i=1; i<=last_page_of_available_item && last_page_of_available_item != 1 ;i++) {
						strip_element.append('<li><a href="javascript:shDragging.show_page(\'available_item\','+i+')"></a></li>');
					}
				} else if (mode == 'page'){
					for(var i=1;i<=last_page_of_available_item;i++) {
						strip_element.append('<li><a href="javascript:shDragging.show_page(\'available_item\','+i+')"></a></li>');
					}
				}
					
				ul_element.children("li").hide();
				var k=(showing_page_of_available_item-1)*available_item_per_page;
				for(j=k;j<k+available_item_per_page;j++)
					ul_element.children("li").eq(j).show();
				strip_element.children('li').eq(showing_page_of_available_item-1).children('a').attr('class','current');

				if(mode == 'page'){
					if(strip_element.find("li").length == 1) {
						strip_element.find('li').remove();
					}
				}
			},
			refresh_installed_app_in_page_panel : function() {
				var ul_element=$(".left-panel").find('.dragging-app div').find('ul');
				var strip_element=$(".left-panel").find('.strip ul').empty();
				
				if(mode == 'company'){
					for(var i=1;i<=last_page_of_installed_app_in_page && last_page_of_installed_app_in_page != 1 ;i++){
						strip_element.append('<li><a href="javascript:shDragging.show_page(\'installed_app_in_page\','+i+')"></a></li>');
					}
				} else if (mode == 'page'){
					for(var i=1;i<=last_page_of_installed_app_in_page;i++) {
						strip_element.append('<li><a href="javascript:shDragging.show_page(\'installed_app_in_page\','+i+')"></a></li>');
					}
				}
				
				ul_element.children("li").not(ul_element.children("li:first")).hide();
				var k=(showing_page_of_installed_app_in_page-1)*installed_app_in_page_per_row;
				for(j=k;j<k+installed_app_in_page_per_row;j++){
					ul_element.children("li").eq(j+1).show();
				}
				strip_element.children('li').eq(showing_page_of_installed_app_in_page-1).children('a').attr('class','current');
				
				if(mode == 'page'){
					//Remove pagination if there is one page
					if(strip_element.find("li").length == 1) {
						strip_element.find('li').remove();
					}
				}
			},
			show_installed_app_in_page : function(page_id,facebook_page_id) {
				var left_panel = $(".left-panel");
				if(mode == 'company'){	//If page is not installed, don't show apps
					if(shDragging.get_page_installed(page_id)==0)
					{
						$('.head-box-app-list').hide();
						$('div.dragging-app').hide();
						popup = $('#hidden-notice').find('.goto-facebook.page-installed').clone();
						popup.find(".bt-go-facebook").attr('href', base_url+'tab/facebook_page/'+page_id);
						$(".notice").html(popup);
						$(".notice").addClass('warning').show(); 
						return false;
					}
				}
				set_loading();
				jQuery.ajax({
					async:true,
					url: base_url + "page/json_get_installed_apps/" + page_id,
					dataType: "json",
					beforeSend: function() {
						if(mode == 'company'){
							left_panel.find('.dragging-app div').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
							$(".head-box-app-list").hide();
						} else if (mode == 'page'){
							left_panel.find('.dragging-app div').html("<div class='loading'></div><ul></ul>");
							$(".head-dragging-app").hide();
						}
					},
					success: function(json) {
						var ul_element;
						if(mode == 'company'){
							$(".notice").hide();
							ul_element=left_panel.find('.dragging-app div').find('ul').css('min-height', '127px').empty();
							ul_element.append('<li class="add-app"></li>');
							//Signup Form
							ul_element.append('<li><p><img class="app-image" src="'+imgsize(base_url+'assets/images/apps/page-signup/app_image_s.png','normal')+'" />'
								+'<span class="button">'
								+'<a class="bt-setting_app" href="'+base_url+'settings/page_signup_fields/'+page_id+'"><span>Setting</span></a>'
								+'</span>'
								+'</p><p class="appname">'+ 'Page Signup Form' +'</p><input type="hidden" class="app_install_id" value="'+0+'" /></li>');
							for(i in json) {
								ul_element.append('<li><p><img class="app-image" src="'+imgsize(json[i].app_image,'normal')+'" />'
								+'<span class="button">'
								+'<a class="bt-setting_app" href="'+base_url+'app/config/'+json[i].app_install_id+'"><span>Setting</span></a>'
								+'</span>'
								+'</p><p class="appname">'+ json[i].app_name +'</p><input type="hidden" class="app_install_id" value="'+json[i].app_install_id+'" /></li>');
							}
						} else if (mode == 'page'){
							ul_element=left_panel.find('.dragging-app div').find('ul').css('min-height', '127px').empty();
							for(i in json){
								ul_element.append('<li><p><img class="app-image" src="'+imgsize(json[i].app_image,'normal')+'" alt="" width="64" height="64" />'
									+'<span class="button">'
									+'<a class="bt-update_app" href="'+base_url+'app/'+json[i].app_install_id+'"><span>Update</span></a>'
									+'<a class="bt-setting_app" href="'+base_url+'o_setting/'+page_id+'/app/'+json[i].app_install_id+'"><span>Setting</span></a>'
									+'</span>'
									+'</p><p class="appname">'+ json[i].app_name +'</p><input type="hidden" class="app_install_id" value="'+json[i].app_install_id+'" /></li>');
							}			
						}
						showing_page_of_installed_app_in_page=1;
						last_page_of_installed_app_in_page=Math.ceil(json.length/installed_app_in_page_per_row);
						shDragging.refresh_installed_app_in_page_panel();	
						ul_element.droppable({
							drop: function(e, ui) {
								sorted=false;
								dragging_object=$(ui.draggable);
								// //console.log(e,ui,ui.draggable);
							},
							accept:"li.draggable"
						}).sortable({
							items: 'li:not(.add-app)',
							placeholder: "ui-state-highlight",
							revert: true,
							stop: function(e,ui){
								if(!sorted) {
									sorted=true;
									var app_id=dragging_object.children('input.app_id').val();
									var app_secret_key=dragging_object.children('input.app_secret_key').val();
									var app_api_key=dragging_object.children('input.app_api_key').val();
									var app_install_url=dragging_object.children('input.app_install_url').val();
									dragging_object.removeClass('draggable');
									
									app_install_url=app_install_url.replace("{company_id}",company_id)
												.replace("{user_id}",user_id)
												.replace("{page_id}",page_id)+"&force=1";		
									set_loading();
									
									jQuery.ajax({
										async:true,
										url: base_url+"app/json_add_to_page",
										dataType: "json",
										type: "POST",
										data: {
											app_id:app_id,
											install_url:app_install_url,
											facebook_page_id:facebook_page_id,
											facebook_app_id:app_api_key,
											user_id:user_id,
											page_id:page_id,
											company_id:company_id
										},
										error: function(jqXHR, textStatus, errorThrown) {
											shDragging.show_installed_app_in_page(page_id,facebook_page_id);
											shDragging.show_available_app_in_page(page_id);
											//console.log("app/curl failed 3 " + errorThrown);
										},			
										success: function(json) {
											if(json!=null&&json.status!=null&&json.status.toUpperCase()=="OK") {
												var app_install_id=json.app_install_id;
												dragging_object.append('<input type="hidden" value="'+app_install_id+'" class="app_install_id" />');
												dragging_object.children('p:first').append('<span class="button">'
												+'<a class="bt-setting_app" href="'+base_url+'app/config/'+app_install_id+'"><span>Setting</span></a>'
												+'</span>');
												shDragging.refresh_installed_app_in_page_panel();
												shDragging.show_available_app_in_page(page_id);
												//update company installed apps count
												$.getJSON(base_url + "company/json_get_installed_apps_count_not_in_page/" + company_id, function(json) {
													$(".app-installed-count").html("Application (" + json.app_count + ")");
													$("#info-installed-app").html('<span>Installed Application</span>'+json.app_count);
												});
												// $.getJSON(base_url + "company/json_get_installed_apps_count_not_in_page/" + company_id, function(json){
													// $(".app-installed-count").html("Application (" + json.app_count + ")");
												// });
												shDragging.update_app_order_in_dashboard();
												if(mode == 'company'){
													popup = $('#hidden-notice').find('.goto-facebook.app-installed').clone();
													popup.find(".bt-go-facebook").attr('href', json.facebook_tab_url);
													$.fancybox({
														content: popup
													});
												} else if (mode == 'page'){
													parent.add_app_complete(json.facebook_tab_url);
												}
											} else {
												if(mode == 'company'){
													shDragging.show_installed_page_in_company();
												}
												shDragging.show_installed_app_in_page(page_id,facebook_page_id);
												shDragging.show_available_app_in_page(page_id);
												//console.log("app/curl json mismatch 3 : " + json);
											}
										},
									});
								} else {
									shDragging.update_app_order_in_dashboard();
								}
							}
						});
						
						if(mode == 'company'){
							$(".add-app").click( function() {
								shDragging.add_app_button_click();
								//get company available pages
								shDragging.show_available_app_in_page(page_id);
							});
						} else if (mode == 'page'){
							$(".head-dragging-app b").html(page_name);
						}
						
						$(".head-box-app-list strong").html(json.length+' Applications installed in');
						$(".head-box-app-list").show();
						left_panel.find('.loading').remove();
					}
				});
			},
			show_available_app_in_page : function(page_id) {
				var left_panel = $(".left-panel");
				var right_panel = $(".right-panel");
				jQuery.ajax({
					async:true,
					url: base_url + "company/json_get_not_installed_apps/" + company_id + "/" + page_id,
					dataType: "json",
					beforeSend: function() {
						if(mode == 'company'){
							right_panel.find('.dragging-app').html("<div class='loading' align='center'><img src='"+base_url+"assets/images/loading.gif' /><br />Loading</div><ul></ul>");
						} else if (mode == 'page'){
							right_panel.find('.dragging-app').html("<div class='loading'></div><ul></ul>");
						}
					},
					success: function(json) {
						var ul_element=right_panel.find('.dragging-app').find('ul');
						for(i in json) {
							ul_element.append(
							'<li class="draggable"><p><img class="app-image" src="'+imgsize(json[i].app_image,'normal')+'" alt="" width="64" height="64" /></p>'
							+'<p class="appname">'+ json[i].app_name +'</p>'
							+"<input class='app_id' type='hidden' value='" + json[i].app_id + "'/>"
							+"<input class='app_install_url' type='hidden' value='" + json[i].app_install_url + "'/>"
							+"<input class='app_secret_key' type='hidden' value='" + json[i].app_secret_key + "'/>"
							+"<input class='app_api_key' type='hidden' value='" + json[i].app_facebook_api_key + "'/></li>"
							);
						}
						showing_page_of_available_item=1;
						available_item_per_page=9;
						last_page_of_available_item=Math.ceil(json.length/available_item_per_page);
						shDragging.refresh_available_item_panel();
						ul_element.find('li.draggable').draggable({
							connectToSortable: (function(){
								if(mode == 'company'){
									return left_panel.find('.box-app-list').find('ul');
								} else if (mode == 'page'){
									return left_panel.find('.dragging-app div').find('ul');
								}
							})(),
							helper: "clone",
							revert: "invalid",
							drag: function() {
								left_panel.find('.dragging-app div').addClass('in-action');
								if(mode == 'company'){
									$("div.dragging-app ul").css('height','auto');
								}
							},
							stop: function() {
								left_panel.find('.dragging-app div').removeClass('in-action');
								if(mode == 'company'){
									$("div.dragging-app ul").css('height','255px');
								}
							}
						});
						right_panel.find('.loading').remove();
					}
				});
			},
			init : function(mode_init){
				mode = mode_init;
				if(mode == 'company'){
					$('div.popup_company-thanks a.bt-continue').live('click', function () {
						$.fancybox.close();
					});
					
					shDragging.get_activity_log();
					$(".add-page").live('click', function() {
						shDragging.add_page_button_click();
						//get company available pages
						shDragging.show_available_page_in_company();
					});
					$(".bt-create_page").live('click', function() {
						shDragging.create_new_page_button_click();
						//get company available pages
						shDragging.show_available_page_in_company();
						//get installed pages
						shDragging.show_installed_page_in_company();
					});
					shDragging.select_page_tab();
					//get company installed apps count
					$.getJSON(base_url + "company/json_get_installed_apps_count_not_in_page/" + company_id, function(json) {
						$(".app-installed-count").html("Application (" + json.app_count + ")");
						$("#info-installed-app").html('<span>Installed Application</span>'+json.app_count);
					});
				} else if (mode == 'page'){
					shDragging.select_app_tab();
				}
				//get all app install statuses
				$.getJSON(base_url + "app/json_get_all_app_install_status", function(json) {
					for(i in json) {
						all_app_install_statuses[''+json[i].app_install_status] = new Array(json[i].app_install_status_id,json[i].app_install_status_description);
					}
				});
				$( "ul, li" ).disableSelection();
			}
		};
		return shDragging;
	})();
	window.shDragging = shDragging;
})(window);