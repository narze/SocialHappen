$(document).ready(function(){
		
	$(':radio').click(function(){
		if($(':checked').val() != 0)
			$('#target_id-row').hide('slow');
		else
			$('#target_id-row').show('slow');
	});
	
	$('#invite-submit').click(function(){
		
		campaign_id = $('#campaign_id').val();
		app_install_id = $('#app_install_id').val();
		facebook_page_id = $('#facebook_page_id').val();
		user_facebook_id = $('#user_facebook_id').val();
		invite_type = $(':checked').val();
		target_facebook_id = $('#target_facebook_id').val();
		message = $('#message').val();
		
		$.ajax({
				url: base_url+'invite/create_invite/',
				global: true,
				type: "POST",
				data: {
					'campaign_id': campaign_id,
					'app_install_id': app_install_id,
					'facebook_page_id': facebook_page_id,
					'user_facebook_id': user_facebook_id,
					'invite_type': invite_type,
					'target_facebook_id': target_facebook_id,
					'message': message
				},
				dataType: "text",
				async:true,
				success: function(data){
					elements = $.parseJSON(data);
					if(elements){
						if(elements.invite_key){
							$('#invite-key').html(invite_url+elements.invite_key);
							$('#invite-error-div').hide('slow');
							$('#invite-key-div').show('slow');
						}else{
							$('#invite-error').html(elements.error);
							$('#invite-key-div').hide('slow');
							$('#invite-error-div').show('slow');
						}
						console.log(elements);
					}
					
					elements = null;
				}
			}
		).json;
	
	});
	
	
});