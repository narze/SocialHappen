$(function(){	
	
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
