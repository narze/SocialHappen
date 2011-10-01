shregister = function(){
	$.fancybox({
		href: base_url+'tab/signup/'+page_id
	});
	$('form.signup-form').die('submit');
	$('form.signup-form').live('submit', function() {
		$(this).ajaxSubmit({target:'#signup-form'});
		return false;
	});
	
	$('a.bt-register-now').live('click', function(){
		$('form.signup-form').ajaxSubmit({target:'.popup-fb-2col', replaceTarget:true});
		return false;
	});
}
	
function getScript(url, success) {
	var script     = document.createElement('script');
		 script.src = url;
	var head = document.getElementsByTagName('head')[0],
	done = false;

	// Attach handlers for all browsers
	script.onload = script.onreadystatechange = function() {
			if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
			done = true;
					// callback function provided as param
					success();

					script.onload = script.onreadystatechange = null;
					head.removeChild(script);
			};
	};
	head.appendChild(script);
};

getScript('http://code.jquery.com/jquery-latest.min.js', function(){ console.log('jquery loaded');
	getScript(base_url + 'assets/js/common/fancybox/jquery.fancybox-1.3.4.pack.js', function(){console.log('fancybox loaded');
		getScript(base_url+'assets/js/common/jquery.form.js', function(){ console.log('form loaded');
			onLoad();
		});
	});
});

onLoad = function(){
	$(function(){
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
		
		socialhappen_popup();
	});
};

socialhappen_popup = function(){
	if(view_as == 'guest'){
		$.fancybox({
			href: base_url+'tab/guest'
		});
		$('a.bt-don-awesome').die('click');
		$('a.bt-don-awesome').live('click',function(){
			$.fancybox.close();
		});
	} else if(view_as == 'admin'){
		if(page_app_installed_id!=0) {
			$.fancybox({
				href: base_url+'tab/app_installed/'+ page_app_installed_id
			});
			$('a.bt-stay_fb').die('click');
			$('a.bt-stay_fb').live('click',function(){
				$.fancybox.close();
			});
			page_app_installed_id=0;
		} else if(page_installed==0){
			$.fancybox({
				href: base_url+'tab/page_installed/'+ page_id
			});
			$('a.bt-stay_fb').die('click');
			$('a.bt-stay_fb').live('click',function(){
				$.fancybox.close();
			});
			page_installed=1;
		}
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
 }