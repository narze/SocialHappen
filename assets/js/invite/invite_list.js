$(document).ready(function(){
	
	$('#report-submit').click(function(){
	
		campaign_id = $('#campaign_id').val();
		app_install_id = $('#app_install_id').val();
		facebook_page_id = $('#facebook_page_id').val();
		user_facebook_id = $('#user_facebook_id').val();
		
		$.ajax({
				url: base_url+"invite/invite_list_fetch",
				global: true,
				type: "POST",
				data: {
					'campaign_id': campaign_id,
					'app_install_id': app_install_id,
					'facebook_page_id': facebook_page_id,
					'user_facebook_id': user_facebook_id
				},
				dataType: "text",
				async:true,
				success: function(data){
					elements = $.parseJSON(data);
					
					console.log(elements);
					
					$('#invite-list-div').show('slow');
					
					$('#invite-list').html('');
					for(x in elements)
						$('#invite-list').append('<div><a href="'+base_url+'invite/invite_status/'+elements[x].invite_key+'">'+elements[x].invite_key+'</a> invite_count : '+elements[x].invite_count+'<div>');
													
					elements = null;
				}
			}
		).json;
	
	});
});