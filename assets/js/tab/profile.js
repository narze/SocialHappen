$(function(){
	$('#profile-tab ul li a').live('click',function(){
		set_loading();
		$('div#profile-content').load($(this).attr('href'));
		return false;
	});
});