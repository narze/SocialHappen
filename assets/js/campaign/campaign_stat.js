$(function(){	
	
});

function render_stat(campaign_id){
	if(!$('div#campaign-stat').html()){
		$('div#campaign-stat').load(base_url + 'campaign/get_stat_graph/'+campaign_id);
	}
}
