$(function(){
	
	// $('div#main').load(base_url+'settings/'+setting_name+'/'+param_id,function(){
		// make_form($('li a.company-page-setting,li a.user-company-setting,li a.account-setting,li a.company-page-list'));
	// });
	
	$('li a.company-page-setting,li a.user-company-setting,li a.account-setting,li a.company-page-list').live('click',function(){
		$('div#main').load($(this).attr('href'));
		make_form($(this));
		return false;
	});
	
	
	function make_form(element){
		$('form').live('submit', function() {
			$(this).ajaxSubmit({target:'div#main',success:function(){form_style(element);}});
			return false;
		});
		form_style(element);
	}
	
	function form_style(element){
		if(element.hasClass("account-setting")){
			
		} else if(element.hasClass("company-page-list")){
	
		} else if(element.hasClass("user-company-setting")){
	
		} else if(element.hasClass("company-page-setting")){
	
		} 
	}
	
	if(setting_name == 'account'){
		$('li a.account-setting').click();
	} else if(setting_name == 'company_pages'){
		$('li a.company-page-list').click();
	} else if(setting_name == 'company'){
		$('li a.user-company-setting').click();
	} else if(setting_name == 'page'){
		$('li a.company-page-setting').click();
	} else if(setting_name == 'package'){
	
	} else if(setting_name == 'reference'){
	
	}
});