$(function(){
	$('form').live('submit', function() {
		var targetSelector = 'div.box-setting:first #'+$(this).attr('class');
		var srcSelector = '#'+$(this).attr('class');
		set_loading();
		$(this).ajaxSubmit({success:function(response){
			alert(response);
			alert(srcSelector);
			alert($(response).filter(srcSelector).html());
			$(targetSelector).replaceWith($(response).filter(srcSelector));
		}});
		return false;
	});
});