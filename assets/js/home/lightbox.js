$(function(){
	
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
	
	if(typeof popup_name != 'undefined' && popup_name != ''){
		$.fancybox({
			href: base_url+popup_name,
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 0,
			scrolling: 'no',
			modal: !closeEnable
		});
	}
	
	$('#create_company').live('click',function(){
		$.fancybox({
			href: base_url+'bar/create_company',
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 0,
			scrolling: 'no'
		});
		$('#create-company-form').load(base_url+'bar/create_company_form',label);

		$('form.create-company-form').die('submit');
		$('form.create-company-form').live('submit',function(){
			$(this).ajaxSubmit({target:'#create-company-form'});
			return false;
		});
		return false;
	});
});
