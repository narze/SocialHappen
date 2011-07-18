function render_stat(page_id){
	if(!$('div#page-report').html()){
		set_loading();
		$('div#page-report').load(base_url + 'page/get_stat_graph/'+page_id);
	}
}
