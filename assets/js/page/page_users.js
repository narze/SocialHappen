$(function(){	
	$.getJSON(base_url+'page/json_get_users/'+page_id,function(json){
		for(i in json){
			var row = $('.wrapper-details.users .details table tr.hidden-template').clone()
			.removeClass('hidden-template')
			.appendTo('.wrapper-details.users .details table');

			var app_list = row.find('td.app-list div');
			app_list.find('p.thumb img').attr('src', json[i].user_image);
			app_list.find('h2,p.thumb').append('<a href="'+base_url+'page/'+page_id+'/user/'+json[i].user_id+'">'+json[i].user_first_name+' '+json[i].user_last_name+'</a>');
			app_list.find('p.email').append(json[i].user_email);
			
			row.find('td.status.app-status span').append('installed');
			row.find('td.status.app-member b').append('user');
			row.find('td.status.app-monthly-active b').append('active');
			row.find('td.bt-icon a.bt-edit').attr('href', base_url+'path/to/edit/'+ json[i].app_install_id);
			row.find('td.bt-icon a.bt-setting').attr('href', base_url+'path/to/setting/'+ json[i].app_install_id);
			row.find('td.bt-icon a.bt-delete').attr('href', base_url+'path/to/delete/'+ json[i].app_install_id);
			row.find('td.bt-icon a.bt-go').attr('href', base_url+'path/to/go/'+ json[i].app_install_id);
		}
		$('.wrapper-details.users .details table tr:even').addClass('next');
	});
	
	//paging
});

