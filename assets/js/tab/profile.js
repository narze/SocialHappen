$(function(){ 
	$('a.a-profile').live('click',function(){
		set_loading();
		$('div#main').load(base_url+'tab/profile/'+user_id+'/'+token,function(){
		
		});
		return false;
	});
});