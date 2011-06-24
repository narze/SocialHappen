$(function(){	
	$.getJSON(base_url+'page/json_get_installed_apps/'+page_id,function(json){
		for(i in json){
			var row = $('.wrapper-details.apps .details table tbody tr.hidden-template').clone()
			.removeClass('hidden-template')
			.appendTo('.wrapper-details.apps .details table tbody');

			var app_list = row.find('td.app-list div');
			app_list.find('p.thumb img').attr('src', json[i].app_image);
			app_list.find('h2').append('<a href="'+base_url+'app/'+json[i].app_install_id+'">'+json[i].app_name+'</a>');
			app_list.find('p.description').append(json[i].app_description);
			
			row.find('td.status.app-status span').append('installed');
			row.find('td.status.app-member b').append('user');
			row.find('td.status.app-monthly-active b').append('active');
			row.find('td.bt-icon a.bt-edit').attr('href', base_url+'path/to/edit/'+ json[i].app_install_id);
			row.find('td.bt-icon a.bt-setting').attr('href', base_url+'path/to/setting/'+ json[i].app_install_id);
			row.find('td.bt-icon a.bt-delete').attr('href', base_url+'path/to/delete/'+ json[i].app_install_id);
			row.find('td.bt-icon a.bt-go').attr('href', base_url+'path/to/go/'+ json[i].app_install_id);
		}
		$('.wrapper-details.apps .details table tbody tr:even').addClass('next');
	});
	
	//paging
	
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
	})
});

