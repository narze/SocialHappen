$(function(){
	$('li.company-page,li.user-company a').live('click',function(){
		$('div#main').load($(this).attr('href'));
		return false;
	});
});