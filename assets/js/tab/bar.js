$(function(){
	$('#bar ul li a').live('click',function(){
		$('div#main').load($(this).attr('href'));
		return false;
	});
});