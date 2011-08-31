$(function(){
	for(i in user_companies){
		$.ajax({
			url: base_url+'company/json_get_profile/' + user_companies[i].company_id,
			dataType: 'json',
			async: false,
			success: function(data){
				$('.goto div ul').append('<li class="goto-list-company-'+user_companies[i].company_id+'"><p class="thumb"><img src="'+imgsize(data.company_image,'square')+'" alt="" /></p><h2><a href="'+base_url+'company/'+data.company_id+'">'+data.company_name+'</a></h2></li>');
				$.getJSON(base_url+'company/json_get_pages/' + user_companies[i].company_id, function(data) {
					if(data.length>0) {
						$.each(data, function(i,item){
							$('.goto-list-company-'+ item.company_id).append('<p class="goto-list-company-page-'+item.page_id+'">&raquo; <a href="'+base_url+'page/'+item.page_id+'">'+item.page_name+'</a></p>');
						});
					} else {
						$('.goto-list-company-'+ user_companies[i].company_id).append('<p>No page yet</p><p><a href="'+base_url+'company/'+user_companies[i].company_id+'">+ add new page</a></p>');
					}
				});
			}
		});
		// $.ajaxSetup({'async': false});
		// $.getJSON(base_url+'company/json_get_profile/' + user_companies[i].company_id, function(data) {
			// $('.goto div ul').append('<li class="goto-list-company-'+user_companies[i].company_id+'"><p class="thumb"><img src="'+imgsize(data.company_image,'square')+'" alt="" /></p><h2><a href="'+base_url+'company/'+data.company_id+'">'+data.company_name+'</a></h2></li>');
		// });
		// $.getJSON(base_url+'company/json_get_pages/' + user_companies[i].company_id, function(data) {
			// if(data.length>0) {
				// $.each(data, function(i,item){
					// $('.goto-list-company-'+ item.company_id).append('<p class="goto-list-company-page-'+item.page_id+'">&raquo; <a href="'+base_url+'page/'+item.page_id+'">'+item.page_name+'</a></p>');
				// });
			// } else {
				// $('.goto-list-company-'+ user_companies[i].company_id).append('<p>No page yet</p><p><a href="'+base_url+'company/'+user_companies[i].company_id+'">+ add new page</a></p>');
			// }
		// });
	}
	
	var companyname;
	var companydetail;

	function label() {
		
		//Company name
		companyname = $('#company_name').val('Company name').addClass('inactive');
		companyname.focus(function () {
			if($(this).val() == 'Company name') $(this).val('').removeClass('inactive');
		});
		companyname.blur(function () {
			if($(this).val() == '') $(this).val('Company name').addClass('inactive');
		});
		
		//Company detail
		companydetail = $('#company_detail').val('Company detail').addClass('inactive');
		companydetail.focus(function () {
			if($(this).val() == 'Company detail') $(this).val('').removeClass('inactive');
		});
		companydetail.blur(function () {
			if($(this).val() == '') $(this).val('Company detail').addClass('inactive');
		});
		
	}
	
	$('.bt-create_company').live('click',function(){
		$.fancybox({
			href: base_url+'bar/create_company',
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 0,
			scrolling: 'no',
			onComplete: function(){
				$('#create-company-form').load(base_url+'bar/create_company_form', label);
				$('form.create-company-form').die('submit');
				$('form.create-company-form').live('submit',function(){
					$(this).ajaxSubmit({target:'#create-company-form'});
					return false;
				});
			}
		});
		
		
		return false;
	});
	
	$('.bt-continue').live('click',function(){
		
		if($('#company_name').attr('class') == 'inactive') $('#company_name').val('');
		if($('#company_detail').attr('class') == 'inactive') $('#company_detail').val('');
		
		$('form.create-company-form').die('submit');
		$('form.create-company-form').ajaxSubmit({target:'#create-company-form',success:label});
		return false;
	});
});