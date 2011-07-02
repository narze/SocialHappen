$(function(){	
	//alert('555');
	
});

function render_stat(page_id){
	if(!$('div#page-report').html()){
		//$('div#page-report').load(base_url + 'page/get_stat_graph/'+page_id+'/'+start_date+'/'+end_date);
		$('div#page-report').load(base_url + 'page/get_stat_graph/'+page_id);
	}
}
