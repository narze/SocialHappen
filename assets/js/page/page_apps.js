$(function(){
	function get_page_apps(page_index, jq){
		$.getJSON(base_url+'page/json_get_installed_apps/'+page_id+'/'+per_page+'/'+(page_index * per_page),function(json){
			$('.wrapper-details.apps .details table tr.hidden-template').siblings().addClass('old-result');
			if(json.length == 0) {
				$('.wrapper-details.apps .details').html(
					'<p>No app yet</p> <p><a href="#">+ add new app</a> | <a href="#">help</a></p>'
				);
			} else {
				for(i in json){
					var row = $('.wrapper-details.apps .details table tr.hidden-template').clone()
					.removeClass('hidden-template')
					.appendTo('.wrapper-details.apps .details table');

					var app_list = row.find('td.app-list div');
					app_list.find('p.thumb img').attr('src', imgsize(json[i].app_image,64));
					app_list.find('h2').append('<a href="'+base_url+'app/'+json[i].app_install_id+'">'+json[i].app_name+'</a>');
					app_list.find('p.description').append(json[i].app_description);
					
					row.find('td.status.app-status span').append(json[i].app_install_status_name);
					row.find('td.status.app-member b').append('[count]');
					row.find('td.status1.app-monthly-active b').append('[count]');
					row.find('td.bt-icon a.bt-edit').attr('href', base_url+'app/'+ json[i].app_install_id);
					row.find('td.bt-icon a.bt-setting').attr('href', base_url+'path/to/settings/'+json[i].app_install_id);
					row.find('td.bt-icon a.bt-delete').attr('href', base_url+'path/to/delete/'+ json[i].app_install_id);
					row.find('td.bt-icon a.bt-go').attr('href', json[i].app_url);
				}
				$('.wrapper-details.apps .details table tr:even').addClass('next');
				
			}
			$('.old-result').remove();
		});
		return false;
	}
	
	$('.tab-content ul li.apps a').click(function(){
		$.getJSON(base_url+"page/json_count_apps/"+page_id,function(count){
			$('.pagination-apps').pagination(count, {
				items_per_page:per_page,
				callback:get_page_apps,
				load_first_page:true
			});
		});
		return false;
	});
	
	//fancybox for adding app to page
	$('a.bt-addnew_app').attr('href',base_url+'/page/addapp_lightbox/'+page_id);
	$('a.bt-addnew_app').fancybox({
		transitionIn: 'elastic',
		transitionOut: 'elastic',
		padding: 0,
		width: 908,
		height: 355,
    	autoDimensions: false,
    	scrolling: 'no',
    	onComplete: function(){		
			select_app_tab();
			//get all app install statuses
			$.getJSON(base_url + "app/json_get_all_app_install_status", function(json){
				for(i in json){
					all_app_install_statuses[''+json[i].app_install_status_name] = new Array(json[i].app_install_status_id,json[i].app_install_status_description);
				}
			});
			
			$( "ul, li" ).disableSelection();
    	}
	});
});
