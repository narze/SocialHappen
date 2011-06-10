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
});

