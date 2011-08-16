$(function(){
	for(i in user_companies){
		$.ajaxSetup({'async': false});
		$.getJSON(base_url+'company/json_get_profile/' + user_companies[i].company_id, function(data) {
			$('.goto div ul').append('<li class="goto-list-company-'+user_companies[i].company_id+'"><p class="thumb"><img src="'+imgsize(data.company_image,'square')+'" alt="" /></p><h2><a href="'+base_url+'company/'+data.company_id+'">'+data.company_name+'</a></h2></li>');
		});
		$.getJSON(base_url+'company/json_get_pages/' + user_companies[i].company_id, function(data) {
			if(data.length>0) {
				$.each(data, function(i,item){
					$('.goto-list-company-'+ user_companies[i].company_id).append('<p class="goto-list-company-page-'+item.page_id+'">&raquo; <a href="'+base_url+'page/'+item.page_id+'">'+item.page_name+'</a></p>');
				});
			} else {
				$('.goto-list-company-'+ user_companies[i].company_id).append('<p>No page yet</p><p><a href="'+base_url+'company/'+user_companies[i].company_id+'">+ add new page</a></p>');
			}
		});
	}
	
	$('.bt-create_company').live('click',function(){
		$.fancybox({
			href: base_url+'home/create_company',
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 0,
			scrolling: 'no'
		});
		
		$('#create-company-form').load(base_url+'home/create_company_form');
	
		$('form.create-company-form').die('submit');
		$('form.create-company-form').live('submit',function(){
			$(this).ajaxSubmit({target:'#create-company-form'});
			return false;
		});
		return false;
	});
	
	$('.bt-continue').live('click',function(){
		
		if($('#company_name').attr('class') == 'inactive') $('#company_name').val('');
		if($('#company_detail').attr('class') == 'inactive') $('#company_detail').val('');
		
		$('form.create-company-form').die('submit');
		$('form.create-company-form').ajaxSubmit({target:'#create-company-form'});
		return false;
	});
});