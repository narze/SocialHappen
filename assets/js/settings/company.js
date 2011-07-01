$(function(){
	$('form.company').live('submit', function() {
		$(this).ajaxSubmit({target:'.form'});
		return false;
	});
});