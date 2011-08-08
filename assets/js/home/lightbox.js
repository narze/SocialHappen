$(function(){
	if(popup_name != ''){
		$.fancybox({
			href: base_url+'home/'+popup_name,
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 20,
			scrolling: 'no'
		});
	}
	
	$('#create_company').live('click',function(){
		$.fancybox({
			href: base_url+'home/create_company',
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 20,
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
});
