	$(document).ready(function(){
			
			$('#invite-accept').click(function(){
				invite_key = $('#invite_key').val();				
				target_facebook_id = $('#target_facebook_id').val();				
				
				$.ajax({
						url: base_url+'invite/accept_invite_fetch',
						global: true,
						type: "POST",
						data: {
							'invite_key': invite_key,
							'target_facebook_id': target_facebook_id
						},
						dataType: "text",
						async:true,
						success: function(data){
							elements = $.parseJSON(data);
							console.log(elements);
							if(!elements.error)
								result = 'Invite is accepted successfully';
							else
								result = elements.error;
								
							$('#invite-result').html(result);
							$('#invite-result-block').show('slow');
							elements = null;
						}
					}
				).json;
			
			});
		});