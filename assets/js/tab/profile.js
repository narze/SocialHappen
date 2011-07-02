$(function(){
	$('#profile-tab ul li a').live('click',function(){
		$('div#profile-content').load($(this).attr('href'));
		return false;
	});
});