$(function(){	
	function get_campaign_users(page_index, jq){
		$.getJSON(base_url+'campaign/json_get_users/'+campaign_id,function(json){
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

					var campaign_list = row.find('td.app-list div');
					campaign_list.find('p.thumb img').attr('src', json[i].user_image);
					campaign_list.find('h2,p.thumb').append('<a href="'+base_url+'user/campaign/'+json[i].user_id+'/'+campaign_id+'">'+json[i].user_first_name+' '+json[i].user_last_name+'</a>');
					campaign_list.find('p.email').append(json[i].user_email); //Facebook profile link
					//add : last active & joined date
					row.find('td.status.campaign-status span').append(json[i]); //star point
					row.find('td.status.campaign-member b').append('user'); //hcampaigny point
					row.find('td.status.campaign-monthly-active b').append('active'); //friends count
					row.find('td.bt-icon a.bt-edit').attr('href', base_url+'path/to/edit/'+ json[i].campaign_id); //go to user
					
					row.find('td.bt-icon a.bt-setting').attr('href', base_url+'path/to/setting/'+ json[i].campaign_id); //delete
					row.find('td.bt-icon a.bt-delete').attr('href', base_url+'path/to/delete/'+ json[i].campaign_id); //detele
					row.find('td.bt-icon a.bt-go').attr('href', base_url+'path/to/go/'+ json[i].campaign_id); //delete
				}
				$('.wrapper-details-member.users .details table tr:even').addClass('next');
				$('.old-result').remove();
			}
		});
		return false;
	}

	$('.pagination-users').pagination(user_count, {
        items_per_page:per_page,
        callback:get_campaign_users,
		load_first_page:true
	});
});
