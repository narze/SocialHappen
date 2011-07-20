$(function(){
	$('.menu .profile ul li a').live('click',function(){
		set_loading();
		$('div#main').load($(this).attr('href'));
		return false;
	});
});