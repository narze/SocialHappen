var add_app_complete = function(facebook_tab_url){
	var notice = $('#hidden-notice').find('.goto-facebook.app-installed');
	var popup = notice.clone();
	popup.find(".bt-go-facebook").attr('href', facebook_tab_url);
	$.fancybox({
		content: popup
	});
}

$(function(){
	function get_page_apps(page_index, jq){
		set_loading();
		$.getJSON(base_url+'page/json_get_installed_apps/'+page_id+'/'+per_page+'/'+(page_index * per_page),function(json){
			$('.wrapper-details.apps .details table tr.hidden-template').siblings().addClass('old-result');
			if(json.length == 0) {
				
			} else {
				for(i in json){
					var row = $('.wrapper-details.apps .details table tr.hidden-template').clone()
					.removeClass('hidden-template')
					.appendTo('.wrapper-details.apps .details table');

					var app_list = row.find('td.app-list div');
					app_list.find('p.thumb img').attr('src', imgsize(json[i].app_image,'normal')).addClass('app-image');
					app_list.find('p.thumb img').attr('title', json[i].app_name);
					app_list.find('h2').append('<a href="'+base_url+'app/'+json[i].app_install_id+'">'+json[i].app_name+'</a>');
					app_list.find('p.description').append(json[i].app_description);
					
					row.find('td.status.app-status span').append(json[i].app_install_status);
					row.find('td.status.app-member b').append(json[i].app_member);
					row.find('td.status1.app-monthly-active b').append(json[i].app_monthly_active_member);
					row.find('td.bt-icon a.bt-edit').attr('href', base_url+'app/'+ json[i].app_install_id);
					row.find('td.bt-icon a.bt-setting').attr('href', base_url+'settings/page_apps/app/'+page_id+'/'+json[i].app_install_id);
					//row.find('td.bt-icon a.bt-delete').attr('href', base_url+'path/to/delete/'+ json[i].app_install_id);
					if(json[i].facebook_tab_url) {
						row.find('td.bt-icon a.bt-go').attr('href', json[i].facebook_tab_url);
					} else {
						row.find('td.bt-icon a.bt-go').attr('href', json[i].app_url);
					}
				}
				$('.wrapper-details.apps .details table tr:even').addClass('next');
				
			}
			$('.old-result').remove();
		});
		
		if($('div.pagination-apps').find('a').length == 0) {
			$('div.pagination-apps').find('div.pagination').remove();
		}
		
		return false;
	}
	
	$('.tab-content ul li.apps a').click(function(){
		$.getJSON(base_url+"page/json_count_apps/"+page_id,function(count){
			if(count==0){
				$('.wrapper-details.apps .details').html(
					'<p>No app yet</p> <p><a class="a-addapp">+ add new app</a> | <a href="#">help</a></p>'
				);
			}
			$('.pagination-apps').pagination(count, {
				items_per_page:per_page,
				callback:get_page_apps,
				load_first_page:true,
				next_text:null,
				prev_text:null
			});
		});
		return false;
	});
	
	//fancybox for adding app to page
	$('a.bt-addnew_app,a.a-addapp, a.bt-add_app').live('click',function(){
		$.fancybox({
			href:base_url+'page/addapp_lightbox/'+page_id,
			type:'iframe',
			transitionIn: 'elastic',
			transitionOut: 'elastic',
			padding: 0,
			width: 908,
			height: 518, //TODO : height = 100% of inner content
			autoDimensions: false,
			scrolling: 'no',
			onStart: function() {
				//$("<style type='text/css'> #fancybox-wrap{ top:550px !important;} </style>").appendTo("head");
			}
		});
	});
});
