$(function(){	
	function get_page_users(page_index, jq){
		set_loading();
		$.getJSON(base_url+'page/json_get_users/'+page_id+'/'+per_page+'/'+(page_index * per_page),function(json){
			$('.wrapper-details-member.users .details table tbody tr.hidden-template').siblings().addClass('old-result');
			if(json.length == 0) {
				$('.wrapper-details-member.users .details').html(
					'<p>No user yet</p> <p><a>+ add new user</a> | <a>help</a></p>'
				);
			} else {
				for(i in json){
					var row = $('.wrapper-details-member.users .details table tr.hidden-template').clone()
					.removeClass('hidden-template')
					.appendTo('.wrapper-details-member.users .details table');

					var app_list = row.find('td.app-list div');
					app_list.find('p.thumb img').attr('src', imgsize(json[i].user_image,'square')).addClass('user-image');
					app_list.find('h2').append('<a href="'+base_url+'user/page/'+json[i].user_id+'/'+page_id+'">'+json[i].user_first_name+' '+json[i].user_last_name+'</a>');
					app_list.find('p.email').append(json[i].user_email); //Facebook profile link
					//add : last active & joined date
					row.find('td.status.app-status span').append(json[i]); //star point
					row.find('td.status.app-member b').append('user'); //happy point
					row.find('td.status.app-monthly-active b').append('active'); //friends count
					row.find('td.bt-icon a.bt-edit').attr('href', base_url+'path/to/edit/'+ json[i].app_install_id); //go to user
					
					row.find('td.bt-icon a.icon-user-card').attr('href', base_url+'user/page/'+json[i].user_id+'/'+page_id);
					row.find('td.bt-icon a.bt-go').attr('data-pageuserid', json[i].user_id);
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
		$.getJSON(base_url+"page/json_count_users/"+page_id,function(count){
			$('.pagination-users').pagination(count, {
				items_per_page:per_page,
				callback:get_page_users,
				load_first_page:true,
				next_text:'>',
				prev_text:'<'
			});
		});
		return false;
	});
	
	$('.wrapper-details-member.users .details a.bt-go').live('click', function(){
		page_user_id = $(this).attr('data-pageuserid');
		$.fancybox({
			href: base_url+'page/json_get_page_user_data/'+ page_user_id + '/' + page_id
		});
		return false;
	});
});
