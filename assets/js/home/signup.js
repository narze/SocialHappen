$(function(){
	$('#signup-form').live('submit', function() {
		$(this).ajaxSubmit({target:'.form'});
		return false;
	});
});