	window.fbAsyncInit = function() {
		FB.init({
			appId  : facebook_app_id,
			channelURL : channel_url, // Channel File
			status : true,
			cookie: true,
			xfbml: true,
			oauth: true
		});
		
		FB.getLoginStatus(function(response) {
		  if (response.authResponse) {
			console.log('resp',response);
			friendSelectorInit();
		  } else {
			console.log('not permitted');
			// load denied page
			//jQuery('body').hide();
			jQuery('.wrapper-content').html('please give me your permission');
			//fire_login();
			jQuery('#fblogin').show();
			
		  }
		});
		
		FB.Canvas.setSize();

	}
					
	function fire_login(response){ //deprecated
		FB.login(function(response) {
			if (response.authResponse) {
				signedRequest = response.authResponse.signedRequest;
				userID = response.authResponse.userID;
				
				console.log(signedRequest);
				console.log(userID);
				
				//window.location.reload()
			}
		}, {scope:'user_about_me,email,publish_stream,user_likes,offline_access'}); 
	}
		
	function friendSelectorInit() {
	  FB.api('/me', function(response) {
		 
		  jQuery("#jfmfs-container").jfmfs({ 
			  max_selected: 15, 
			  max_selected_message: "{0} of {1} selected",
			  friend_fields: "id,name,last_name",
			  pre_selected_friends: [1014025367],
			  exclude_friends: [1211122344, 610526078],
			  sorter: function(a, b) {
				var x = a.last_name.toLowerCase();
				var y = b.last_name.toLowerCase();
				return ((x < y) ? -1 : ((x > y) ? 1 : 0));
			  }
		  });
		  jQuery("#jfmfs-container").bind("jfmfs.friendload.finished", function() { 
			  window.console && console.log("finished loading!");
		  });
		  jQuery("#jfmfs-container").bind("jfmfs.selection.changed", function(e, data) { 
			//window.console && console.log("changed", data);
			var target_list = '';

			for(i in data){
				target_list += data[i].id+',';
			}

			if(target_list.length > 0){
				jQuery('#target_id').val(target_list.substring(0,target_list.length - 1 ));
				console.log(target_list.substring(0,target_list.length - 1 ));
			}
		  });                     
		  
		  $("#logged-out-status").hide();

	  });
	  
	

	}              

$(document).ready(function(){
		
	$('#private_invite:checkbox').click(function(){
		var checkbox = $(this);
		if(checkbox.is(':checked'))
			$('#friend-list').show('slow');
		else
			$('#friend-list').hide('slow');
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