$(function(){
	$('a.a-notification').live('click',function(){
		set_loading();
		
		$('div#main').load(base_url+'tab/notifications/'+user_id,function(){
			
		});
		
		$('li.notificationtoggle').find('ul').toggle();
		return false;
	});
});