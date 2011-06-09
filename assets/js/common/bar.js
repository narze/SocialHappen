$(function(){
	//Go to button
	$('#goto').one('click',function(){
		for(i in user_companies){
			$.ajaxSetup({'async': false});
			$.getJSON(base_url+'company/json_get_profile/' + user_companies[i].company_id, function(data) {
				$('#goto-list').append('<div class="goto-list-company-'+user_companies[i].company_id+'">===Company : '+data.company_name+'</div>');
			});
			$.getJSON(base_url+'company/json_get_pages/' + user_companies[i].company_id, function(data) {
				$.each(data, function(i,item){
					$('.goto-list-company-'+ user_companies[i].company_id).append('<div class="goto-list-company-page-'+item.page_id+'">======Page : '+item.page_name+'</div>');
				});
			});
		}
		$('#goto').bind('click',function(){
			$('#goto-list').toggle();
		});
	});

	//User button
	$('#user-menu').hide();
	$('div ul li.drop').click(function(){
		$('#user-menu').toggle();
	});
});