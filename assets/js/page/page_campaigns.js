$(function(){
	Date.createFromMysql = function(mysql_string){ 
	   if(typeof mysql_string === 'string')
	   {
		  var t = mysql_string.split(/[- :]/);
		  return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);          
	   }
	   return null;   
	}
	
	function get_page_campaigns(page_index, jq){
		var filter = $('div.filter li.active').find('a').attr('data-filter');
		var url = base_url+'page/json_get_campaigns/'+page_id+'/'+per_page+'/'+(page_index * per_page) + '?filter='+filter;
		set_loading();
		$.getJSON(url,function(json){
			$('.wrapper-details.campaigns .details table tr.hidden-template').siblings().addClass('old-result');
			if(json.length == 0) {
			
			} else {
				for(i in json){
					var row = $('.wrapper-details.campaigns .details.campaigns table tr.campaign-row.hidden-template').clone()
					.removeClass('hidden-template')
					.appendTo('.wrapper-details.campaigns .details.campaigns table');

					var campaign_list = row.find('td.app-list div');
					if(json[i].campaign_image) campaign_list.find('p.thumb img').attr('src', imgsize(json[i].campaign_image,'normal'));
					campaign_list.find('h2').append('<a href="'+base_url+'campaign/'+json[i].campaign_id+'">'+json[i].campaign_name+'</a>');
					campaign_list.find('p.description').append(json[i].campaign_description);
					
					row.find('td.status2.campaign-status span').append(json[i].campaign_status_name);
					row.find('td.status2.campaign-visitor b').append(json[i].campaign_visitor);
					row.find('td.status2.campaign-member b').append(json[i].campaign_users);
					row.find('td.status2.remaining-days b').countdown({
						until: Date.createFromMysql(json[i].campaign_end_timestamp),
						format: 'D',
						layout: '{dn}'})
					.removeClass('hasCountdown');
					row.find('td.bt-icon a.bt-go.campaigns').attr('href', base_url+'campaign/'+ json[i].campaign_id);
				}
				$('.wrapper-details.campaigns .details.campaigns table tr:even').addClass('next');
			}
			$('.old-result').remove();
		});
		
		if($('div.pagination-campaigns').find('a').length == 0) {
			$('div.pagination-campaigns').find('div.pagination').remove();
		}
		
		return false;
	}
	
	$('div.filter .campaign-filter').live('click',function(){
		$(this).parent().addClass('active').siblings().removeClass('active');

		var filter = $(this).attr('data-filter');
		var url_count = '';
		switch(filter){
			case 'incoming' : url_count = base_url+"tab/json_count_incoming_campaigns/"+page_id; break;
			case 'active' : url_count = base_url+"tab/json_count_active_campaigns/"+page_id; break;
			case 'expired' : url_count = base_url+"tab/json_count_expired_campaigns/"+page_id; break;
			default : url_count = base_url+"tab/json_count_campaigns/"+page_id; break;
		}
		
		$.getJSON(url_count,function(count){
			if(count==0){
				$('.wrapper-details.campaigns .details table').hide(null,function(){
					if(!$(this).find("p.no-results").show()){
						$("<p></p>").addClass('no-results').appendTo(this).html('No campaign yet');
					}
				});
			} else {
				$('.wrapper-details.campaigns .details table').show();
				$('.wrapper-details.campaigns .details p.no-results').hide();
			}
			$('.pagination-campaigns').pagination(count, {
				items_per_page:per_page,
				callback:get_page_campaigns,
				load_first_page:true,
				next_text:null,
				prev_text:null
			});
		});
		return false;
	}).eq(0).click();
});

