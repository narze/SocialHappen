$(function(){
	var filtered = false;
	var campaign_status_id = 0;
	function get_page_campaigns(page_index, jq){
		var url;
		if(filtered) {url = base_url+"page/json_get_campaigns_using_status/"+page_id+'/'+campaign_status_id+'/'+per_page+'/'+(page_index * per_page);alert('filtered');}
		else {url = base_url+"page/json_get_campaigns/"+page_id+'/'+per_page+'/'+(page_index * per_page);alert('not filtered');}
		$.getJSON(url,function(json){
			$('.wrapper-details.campaigns .details table tbody tr.hidden-template').siblings().addClass('old-result');
			if(json.length == 0) {
				// $('.wrapper-details.campaigns .details').html(
					// '<p>No campaign yet</p> <p><a href="#">+ add new campaign</a> | <a href="#">help</a></p>'
				// );
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
	
	$('.tab-content ul li.campaigns a').click(function(){
		$('.pagination-campaigns').pagination(campaign_count, {
			items_per_page:per_page,
			callback:get_page_campaigns,
			load_first_page:true
		});
	});
	
	// $('.campaign-filter').live('click',function(){
		// if($(this).hasClass('inactive-campaign')){
			// filtered = true;
			// campaign_status_id = 1;
		// } else if($(this).hasClass('active-campaign')){
			// filtered = true;
			// campaign_status_id = 2;
		// } else if($(this).hasClass('expired-campaign')){
			// filtered = true;
			// campaign_status_id = 3;
		// } else {
			// filtered = false;
		// }
		// get_page_campaigns(1);
	// });
});

