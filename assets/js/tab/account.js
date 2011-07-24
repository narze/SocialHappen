$(function(){
	$('form').live('submit', function() {
		var targetSelector = 'div.box-setting:first #'+$(this).attr('class');
		var srcSelector = '#'+$(this).attr('class');
		set_loading();
		$(this).ajaxSubmit({success:function(response){
			$(targetSelector).replaceWith($(response).find(srcSelector));
		}});
		return false;
	});
});