$(function(){	
	var campaign_status_id = '';
	function get_app_campaigns(page_index, jq){
		var url;
		if(campaign_status_id != '') {url = base_url+"app/json_get_campaigns_using_status/"+app_install_id+'/'+campaign_status_id+'/'+per_page+'/'+(page_index * per_page);}
		else {url = base_url+"app/json_get_campaigns/"+app_install_id+'/'+per_page+'/'+(page_index * per_page);}
		$.getJSON(url,function(json){
			$('.wrapper-details.campaigns .details table tr.hidden-template').siblings().addClass('old-result');
			if(json.length == 0) {
				
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
			}
			$('.old-result').remove();
		});
		return false;
	}
	
	$('.tab-content ul li.campaigns a,.campaign-filter').live('click',function(){
		if($(this).hasClass('inactive-campaign')){
			filtered = true;
			campaign_status_id = 1;
		} else if($(this).hasClass('active-campaign')){
			filtered = true;
			campaign_status_id = 2;
		} else if($(this).hasClass('expired-campaign')){
			filtered = true;
			campaign_status_id = 3;
		} else {
			filtered = false;
			campaign_status_id = '';
		}
		
		$.getJSON(base_url+"app/json_count_campaigns/"+app_install_id+"/"+campaign_status_id,function(count){
			$('.pagination-campaigns').pagination(count, {
				items_per_page:per_page,
				callback:get_app_campaigns,
				load_first_page:true
			});
		});
		return false;
		
	});
});

