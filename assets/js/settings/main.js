$(function(){
	$('div#main').load(base_url+'settings/'+setting_name+'/'+param_id,function(){
		$('form.account').live('submit', function() {
			$(this).ajaxSubmit({target:'.form'});
			return false;
		});
	});
});