$(function(){
	$.getJSON(base_url+"app/json_get_pages/"+app_install_id,function(json){
		for(i in json){			
			var row = $('.installed-on.hidden-template').clone()
					.removeClass('hidden-template')
					.insertAfter('.installed-on.hidden-template');

					var page_name = row.find('.page-name');
					page_name.append('<a href="'+base_url+'page/'+json[i].page_id+'"><span>- '+json[i].page_name+'</span></a>');
					page_name.find('span').css('color','white');
		}
	});
});