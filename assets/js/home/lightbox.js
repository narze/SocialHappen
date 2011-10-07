$(function(){
	
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
	
});
