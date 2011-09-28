$(function(){
	$('a.platform-link').live('click',function(){
		window.parent.location.replace($(this).attr('href'));
		return false;
	});
		
	$('.toggle').live('click',function(){	
		$('.toggle').not(this).find('ul').hide();
		$(this).find('ul').toggle();
	});
		
	var mouse_is_inside = false;
	$('.toggle').hover(function(){ 
		mouse_is_inside=true;
	}, function(){ 
		mouse_is_inside=false;
	});

	$("body").mouseup(function(){
		if(! mouse_is_inside) $('.toggle').find('ul').hide();
	});
	
	if(view_as == 'guest'){
		$.fancybox({
			href: base_url+'tab/guest/'+page_id
		});
		$('a.bt-don-awesome').die('click');
		$('a.bt-don-awesome').live('click',function(){
			$.fancybox.close();
		});
	} else if(view_as == 'admin'){
		// if(page_app_installed_id!=0) {
			// $.fancybox({
				// href: base_url+'tab/app_installed/'+ page_app_installed_id
			// });
			// $('a.bt-stay_fb').die('click');
			// $('a.bt-stay_fb').live('click',function(){
				// $.fancybox.close();
			// });
			// page_app_installed_id=0;
		// } else if(page_installed==0){
			// $.fancybox({
				// href: base_url+'tab/page_installed/'+ page_id
			// });
			// $('a.bt-stay_fb').die('click');
			// $('a.bt-stay_fb').live('click',function(){
				// $.fancybox.close();
			// });
			// page_installed=1;
		// }
	} else {
		if(!is_user_register_to_page) {
			$.fancybox({
				href: base_url+'tab/signup_page/'+page_id,
				modal: true
			});
			$('a.bt-register-now').die('click');
			$('a.bt-register-now').live('click',function(){
				$('.signup-form').ajaxSubmit({
					target:'div.popup-fb-2col',
					replaceTarget: true
				});
			});
		}
	}
});