$(function(){	
	function get_app_campaigns(page_index, jq){
		$.getJSON(base_url+"app/json_get_campaigns/"+app_install_id+'/'+per_page+'/'+(page_index * per_page),function(json){
			$('.wrapper-details.apps .details table tbody tr.hidden-template').siblings().addClass('old-result');
			if(json.length == 0) {
				$('.wrapper-details.campaigns .details').html(
					'<p>No campaign yet</p> <p><a href="#">+ add new campaign</a> | <a href="#">help</a></p>'
				);
			} else {
				for(i in json){
					var row = $('.wrapper-details.campaigns .details.campaigns table tr.campaign-row.hidden-template').clone()
					.removeClass('hidden-template')
					.appendTo('.wrapper-details.campaigns .details.campaigns table');

					var campaign_list = row.find('td.app-list div');
					campaign_list.find('p.thumb img').attr('src', json[i].campaign_image);
					campaign_list.find('h2').append('<a href="'+base_url+'campaign/'+json[i].campaign_id+'">'+json[i].campaign_name+'</a>');
					campaign_list.find('p.description').append(json[i].campaign_description);
					
					row.find('td.status.campaign-status span').append(json[i].campaign_status_name);
					row.find('td.status.campaign-visitor b').append(json[i].campaign_visitor);
					row.find('td.status.campaign-member b').append(json[i].campaign_users);
					row.find('td.status.remaining-days b').append(json[i].campaign_remaining_days);
					row.find('td.bt-icon a.bt-go.campaigns').attr('href', base_url+'campaign/'+ json[i].campaign_id);
				}
				$('.wrapper-details.campaigns .details.campaigns table tr:even').addClass('next');
				$('.old-result').remove();
			}
		});
		return false;
	}
	
	$('.pagination-campaigns').pagination(campaign_count, {
        items_per_page:per_page,
        callback:get_app_campaigns,
		load_first_page:true
	});
});

