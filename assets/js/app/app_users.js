$(function(){	
	function get_app_users(page_index, jq){
		set_loading();
		$.getJSON(base_url+'app/json_get_users/'+app_install_id+'/'+per_page+'/'+(page_index * per_page),function(json){
			$('.wrapper-details-member.users .details table tbody tr.hidden-template').siblings().addClass('old-result');
			if(json.length == 0) {
				$('.wrapper-details-member.users .details').html(
					'<p>No user yet</p> <p><a href="#">+ add new user</a> | <a href="#">help</a></p>'
				);
			} else {
				for(i in json){
					var row = $('.wrapper-details-member.users .details table tr.hidden-template').clone()
					.removeClass('hidden-template')
					.appendTo('.wrapper-details-member.users .details table');

					var app_list = row.find('td.app-list div');
					app_list.find('p.thumb img').attr('src', imgsize(json[i].user_image,'square')).addClass('user-image');
					app_list.find('h2').append('<a href="'+base_url+'/user/app/'+json[i].user_id+'/'+app_install_id+'">'+json[i].user_first_name+' '+json[i].user_last_name+'</a>');
					app_list.find('p.email').append(json[i].user_email); //Facebook profile link
					//add : last active & joined date
					row.find('td.status.app-status span').append(json[i]); //star point
					row.find('td.status.app-member b').append('user'); //happy point
					row.find('td.status.app-monthly-active b').append('active'); //friends count
					row.find('td.bt-icon a.bt-edit').attr('href', base_url+'path/to/edit/'+ json[i].app_install_id); //go to user
					
					row.find('td.bt-icon a.bt-setting').attr('href', base_url+'path/to/setting/'+ json[i].app_install_id); //delete
					row.find('td.bt-icon a.bt-delete').attr('href', base_url+'path/to/delete/'+ json[i].app_install_id); //detele
					row.find('td.bt-icon a.bt-go').attr('href', base_url+'path/to/go/'+ json[i].app_install_id); //delete
				}
				$('.wrapper-details-member.users .details table tr:even').addClass('next');
			}
			$('.old-result').remove();
		});
		
		if($('div.pagination-users').find('a').length == 0) {
			$('div.pagination-users').find('div.pagination').remove();
		}
		
		return false;
	}

	$('.tab-content ul li.users a').click(function(){
		$.getJSON(base_url+"app/json_count_users/"+app_install_id,function(count){
			$('.pagination-users').pagination(count, {
				items_per_page:per_page,
				callback:get_app_users,
				load_first_page:true,
				next_text:'>',
				prev_text:'<'
			});
		});
		return false;
	});
});
