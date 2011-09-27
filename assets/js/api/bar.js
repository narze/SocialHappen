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
});