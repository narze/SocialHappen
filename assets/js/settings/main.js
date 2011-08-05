$(function(){
	$('li a.company-page-setting,li a.user-company-setting,li a.account-setting,li a.company-page-list,li a.package-billing').live('click',function(){
		element = $(this);
		set_loading();
		check_login(null,function(){
			$('div#main').load(element.attr('href'));
			make_form(element);
		});
		
		return false;
	});
	
	
	function make_form(element){
		$('form').die('submit');
		$('form').live('submit', function() {
			var targetSelector = 'div#main #'+$(this).attr('class');
			var srcSelector = '#'+$(this).attr('class');
			set_loading();
			$(this).ajaxSubmit({success:function(response){
				$(targetSelector).replaceWith($(response).filter(srcSelector));
				form_style(element);
			}});
			return false;
		});
		form_style(element);
	}
	
	function form_style(element){
		if(element.hasClass("account-setting")){
			$('.date').datepicker({"dateFormat": "yy-mm-dd"});
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