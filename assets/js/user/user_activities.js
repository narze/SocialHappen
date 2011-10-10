$(function(){	
	$.getJSON(base_url+"user/json_count_user_activities/"+page_id+"/"+user_id, function(count){
		$('.activities-table-footer').pagination(count, {
			items_per_page:activities_per_page,
			callback:get_user_activities,
			load_first_page:true,
			next_text: null,
			prev_text: null
		});
	});
});

function render_user_stat(page_id, user_id){
	if(!$('div#user-stat').html()){
		//$('div#page-report').load(base_url + 'page/get_stat_graph/'+page_id+'/'+start_date+'/'+end_date);
		if(page_id.length > 0){
			$('div#user-stat').load(base_url + 'user/get_stat_graph/page/'+user_id+'/'+page_id);
		}else if(app_install_id.length > 0){
			$('div#user-stat').load(base_url + 'user/get_stat_graph/app/'+user_id+'/'+app_install_id);
		}else if(campaign_id.length > 0){
			$('div#user-stat').load(base_url + 'user/get_stat_graph/campaign/'+user_id+'/'+campaign_id);
		}
	}
}

function get_user_activities(page_index) {
	set_loading();
	$.getJSON(base_url+'user/json_get_user_activities/'+page_id+'/'+user_id+'/'+activities_per_page+'/'+(page_index * activities_per_page), function(json){
		if(json.length == 0) {
			$('div.activities-table table tr.no-activity').show();
		} else {
			var old_row = $('div.activities-table table tbody tr'); //Prevent page auto scroll
			for(i in json) {
				var row = $('div.activities-table table tr.hidden-template').clone()
					.removeClass('hidden-template').show()
					.appendTo('div.activities-table table tbody');
				row.find('td.page-name').append( json[i].page_name );
				row.find('td.app-name').append( json[i].app_name );
				row.find('td.campaign-name').append( json[i].campaign_name );
				row.find('td.activity-detail').append( json[i].activity_detail );
				row.find('td.date').append( json[i].date + ' - ' +json[i].time );
			}
			old_row.remove(); //Prevent page auto scroll
		}
	});
	
	if($('div.activities-table-footer').find('a').attr('href') == '#') { 
		$('div.activities-table-footer').find('a').removeAttr('href'); // Remove href="#"
	}
				
	if($('div.activities-table-footer').find('a').length == 0) {
		$('div.activities-table-footer').find('div.pagination').remove();
	}
}