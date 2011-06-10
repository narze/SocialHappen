$(function(){	
	$.getJSON(base_url+"page/json_get_campaigns/"+page_id,function(json){
		for(i in json){
		var row = $('.wrapper-details.campaigns .details.campaigns table tr.campaign-row.hidden-template').clone()
			.removeClass('hidden-template')
			.appendTo('.wrapper-details.campaigns .details.campaigns table');

			var campaign_list = row.find('td.app-list div');
			//campaign_list.find('p.thumb img').attr('src', json[i].campaign_image);
			campaign_list.find('p.thumb img').attr('src', image_url+'cam-icon.png');
			campaign_list.find('h2').append('<a href="'+base_url+'campaign/'+json[i].campaign_id+'">'+json[i].campaign_name+'</a>');
			campaign_list.find('p.description').append(json[i].campaign_description);
			
			row.find('td.status.campaign-status span').append('Active');
			row.find('td.status.campaign-visitor b').append('111');
			row.find('td.status.campaign-member b').append('222');
			row.find('td.status.remaining-days b').append('2');
			row.find('td.bt-icon a.bt-go.campaigns').attr('href', base_url+'path/to/go/'+ json[i].campaign_id);
		}
		$('.wrapper-details.campaigns .details.campaigns table tr:even').addClass('next');
	});
});