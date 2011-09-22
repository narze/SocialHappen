$(function(){
	
	$('ul.platform-apps li a').live('click',function(){
		element = $(this);			
		url = element.attr('href');
		p = get_query(url, 'p');
		c = get_query(url, 'c');
		set_loading();
		check_login(null,function(){
			$('div#main').load(base_url+"configs/"+c+"/"+p);
			make_form(element);
		});
		
		return false;
	});
	
	$('ul.page-apps li a').live('click',function(){
		element = $(this);			
		url = element.attr('href');
		id = get_query(url, 'id');
		set_loading();
		check_login(null,function(){
			$('div#main').load(base_url+"configs/app/"+id);
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
			}});
			return false;
		});
	}
	
	if(config_name == 'signup_fields'){
		$('ul.platform-apps li a#signup-fields').click();
	} else if(config_name == 'badges'){
		$('ul.platform-apps li a#badges').click();
	} else if(config_name == 'app'){
		$('ul.page-apps li a.app[data-appinstallid="'+app_install_id+'"]').click();
	}
	
	$('a.bt-add-field-from-list').live('click', function(){
		$.fancybox({
			content: $('#default-fields').html()
		});
		
		$('a.bt-add-these-field').live('click', function(){
			// apply checkbox to hide, show template fields in form
		});
	});
	
	$('a.bt-create-own-field').live('click', function(){
		$.fancybox({
			content: $('#custom-fields').html()
		});
		
		$('a.bt-add-these-custom-field').live('click', function(){
			// apply checkbox to add custom field into form
		});
	});
});