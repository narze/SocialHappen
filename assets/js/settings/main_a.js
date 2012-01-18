$(function(){
	
	$('li a.company-page-setting,li a.user-company-setting,li a.account-setting,li a.company-page-list,li a.package-billing').live('click',function(){
		element = $(this);		
		element.parents('.menuleft').find('a').removeClass('active');
		element.addClass('active'); 
		url = element.attr('href');
		// s = get_query(url, 's');
		// id = get_query(url, 'id');
		set_loading();
		check_login(null,function(){
			// $('div#main').load(base_url+"o_setting/"+s+"/"+id);
			$('div#main').load(url);
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
			$('body').on('click','.enable-tab, .disable-tab, .enable-bar, .disable-features, .enable-features, .disable-bar', function(){
				var src = $(this).attr('href');
				set_loading();
				$('#facebook-page-information').load(src + ' #facebook-page-information');
				return false;
			});
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
		$('li a.package-billing').click();
	} else if(setting_name == 'reference'){
	
	}
	
	$('ul.detail li.package-apps').live('hover', function () {
		//$('div.package-overlay').slideToggle("slow");
		$('div.package-overlay').toggle();
	});
	
	$('td.billing-popup a').live('click', function () {
		$.fancybox({
			href: $(this).attr('href')
		});
		return false;
	});
	
});