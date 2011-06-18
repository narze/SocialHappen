$(function(){
	$('li.company-page-setting a,li.user-company-setting a,li.account-setting a,li.company-page-list a').live('click',function(){
		$('div#main').load($(this).attr('href'));
		return false;
	});
});