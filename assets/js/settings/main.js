$(function(){
	$('div#main').load(base_url+'settings/'+setting_name+'/'+param_id,function(){
		$('form.account').ajaxForm({target:'.form'});
	});
});