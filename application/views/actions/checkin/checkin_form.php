<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<?php echo link_tag('assets/css/common/jquery.facebook.multifriend.select.css'); ?>
	<?php echo link_tag('assets/css/common/jquery.facebook.multifriend.select-list.css'); ?>

	<style type="text/css">	
		.ui-autocomplete{
			background: #ffffff;
			cursor: default;
		}
  	</style>
</head>
<body>
<?php echo $static_fb_root;?>
<div>
	<h1><?php echo $action_data['data']['checkin_welcome_message']; ?> <?php echo $user['user_first_name']?></h1>
	<div>
		<div id="loading-box" style="display:none;"><img src="<?php echo base_url().'assets/images/loading.gif'; ?>" /></div>
		<form name="" action="<?php echo base_url('actions/checkin/add_user_data_checkin'); ?>" method="post">
			<div><?php echo $action_data['data']['checkin_challenge_message']; ?></div>
			<div>
				<p>Do Check-In at : <?php echo $action_data['data']['checkin_facebook_place_name']; ?></p>
				<p>
					<div id="search-place-normal"> 
						autocomplete location search
						<input type="text" name="search_name" id="search_name" onkeypress="return noenter()" /><br />
					</div>
					<div class="search-toggle" id="place" style="cursor:pointer">
						try basic location search
					</div>
					<div id="search-place-basic" style="display:none"> 
						basic location search
						<input type="text" name="search_place_basic" id="search_place_basic" onkeypress="return noenter()" />
						<input type="button" class="search_basic_submit" data-id="place" value="Search"> 
						<div id="search_place_basic_result">
						</div>
					</div>
				</p>
				<?php if($action_data['data']['checkin_min_friend_count']>0): ?>
					<p>With at least <?php echo $action_data['data']['checkin_min_friend_count']; ?> of your friends</p>
					<p>
						<div id="search-friends-normal"> 
							<div id="friend-list">
								<div id="jfmfs-container"></div> 
							</div> 
						</div>
						<div class="search-toggle" id="friends" style="cursor:pointer">
							try basic friends search
						</div>
						<div id="search-friends-basic" style="display:none">

							selected friends
							<div id="selected-friends-list">-</div>

							basic friends search
							<input type="text" name="search_friends_basic" id="search_friends_basic" onkeypress="return noenter()" />
							<input type="button" class="search_basic_submit" data-id="friends" value="Search"> 

							<div id="search_friends_basic_result">
							</div>
						</div>
					</p>
				<?php endif; ?>
				<p>Message on the post</p>
				<p>
					<input type="text" name="post_message" />
				</p>
			</div>
			<input type="hidden" name="tagged_user_facebook_ids" />
			<input type="hidden" name="facebook_place_id" id="facebook_place_id" />
			<input type="hidden" name="action_data_hash" value="<?php echo $action_data['hash']; ?>" />
			<div>
				<input type="submit" value="submit" />
		</form>
	</div>
</div>

</body>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/common/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url().'assets/js/common/jquery.facebook.multifriend.select.js';?>"></script>
	<script type="text/javascript" src="<?php echo base_url().'assets/js/common/jquery-ui-1.8.20.autocomplete.min.js';?>"></script>

	<script>
		var user_facebook_id = 0;
		var fb_loaded = false;
		var fb_callback_response = '';
		var fb_access_token = '';

		var valid_place_search_result = false;
		var place_search_result = [];
		var place_id_search_result = [];

		var valid_friends_list_result = false;
		var friends_list_result = [];

		var basic_selected_friends_list = [];
		var basic_selected_friends_name_list = [];

		function allow_facebook_login(){
			fb_loaded = true;
		}
	
		function fbcallback(data){
			console.log(data);
			fb_callback_response = data;
			user_facebook_id = data.id;

			fb_callback_response = FB.getAuthResponse();
			fb_access_token = fb_callback_response['accessToken'];

			var facebook_image ='http://graph.facebook.com/'+user_facebook_id+'/picture';
			var facebook_name = data.name;
			var facebook_email = data.email;

			friendSelectorInit();
		}

		function friendSelectorInit() {
			FB.api('/me', function(response) {
			 
				jQuery("#jfmfs-container").jfmfs({ 
					max_selected: 15, 
					max_selected_message: "{0} of {1} selected",
					friend_fields: "id,name,last_name",
					//pre_selected_friends: [1014025367],
					//exclude_friends: [1211122344, 610526078],
					sorter: function(a, b) {
						var x = a.last_name.toLowerCase();
						var y = b.last_name.toLowerCase();
						return ((x < y) ? -1 : ((x > y) ? 1 : 0));
					}
				});

				jQuery("#show-friends").show();
				jQuery("#jfmfs-container").bind("jfmfs.friendload.finished", function() { 
					window.console && console.log("finished loading!");
					jQuery('#jfmfs-inner-header').attr('id','jfmfs-inner-header-show')
				});
				jQuery("#jfmfs-container").bind("jfmfs.selection.changed", function(e, data) { 
					window.console && console.log("changed", data);
					var target_list = '';

					for(i in data){
						target_list += data[i].id+',';
					}

					if(target_list.length > 0){
						jQuery('input[name=tagged_user_facebook_ids]').val(target_list.substring(0,target_list.length - 1 ));
						console.log(target_list.substring(0,target_list.length - 1 ));
					}
				});                     
			});
		}            

		function facebookPlaceSearch(keyword){
			if(fb_access_token==''){
				//console.log('loading');
			}else{
				jQuery('#loading-box').show();
				//console.log('searching');
				//searching criteria : type
				place_search_result = [];
				place_id_search_result = [];
				jQuery('#facebook_place_id').val();
				FB.api('/search?q='+keyword+'&type=place&access_token=' + fb_access_token, searchCallback);
			}
		}  

		function facebookFriendsGet(){
			if(fb_access_token==''){
				//console.log('loading');
			}else{
				FB.api('/me/friends', {fields: 'name,id,location,birthday'}, function(response) {
					friends_list_result = response.data;
					valid_friends_list_result = true;
				});
			}
		}  

		function localFriendListSearch(keyword){
			//local search in friends_list_result -> name contains keyword
			var count = 0;
			basic_friends_search_result = '<ul>';
			for(x in friends_list_result){
				if(friends_list_result[x].name.toLowerCase().indexOf(keyword)!=-1){
					console.log(friends_list_result[x]);
					checked = '';
					if(is_inBasicSelectedFriendList(friends_list_result[x].id))
						checked = 'checked';

					basic_friends_search_result += '<li style="list-style:none;"><input type="checkbox" '+checked+' class="basic_friend_item" value="'+friends_list_result[x].id+'" data-friend_name="'+friends_list_result[x].name+'">'+friends_list_result[x].name+'</li>';
					count++;
				}
			}

			if(count==0){
				basic_friends_search_result += '<li style="list-style:none;">search not found</li>';			
			}
			basic_friends_search_result += '</ul>';

			return basic_friends_search_result;
		}

		//
		function is_inBasicSelectedFriendList(friend_id){
			for(j in basic_selected_friends_list){
				if(basic_selected_friends_list[j] == friend_id)
					return true;
			}

			return false;
		}

		function staticFriendListShow(search_data){
			search_result = localFriendListSearch(search_data);
			//console.log(search_result);
			jQuery('#loading-box').hide();
			jQuery('#search_friends_basic_result').html(search_result);
		}

		function searchCallback(response){
			//console.log(response);
			for(x in response.data){
				place_search_result = place_search_result.concat( { label: response.data[x].name, value: response.data[x].name, place_id: response.data[x].id});
			}
			//console.log(place_search_result);
			jQuery('#loading-box').hide();
			valid_place_search_result = true;
		};
		
		jQuery(document).ready(function(){
			jQuery('#search_name').autocomplete({
											    search: function(event, ui) { 
													valid_place_search_result = false;
											   		facebookPlaceSearch(jQuery(this).val());
											   }
											});

			//select event
			jQuery('#search_name').bind( "autocompleteselect", function(event, ui) {
				//switch showing name and real value
				jQuery('#facebook_place_id').val(ui.item.place_id);
				valid_place_search_result = false;
				console.log(ui.item.place_id);
			});

			jQuery('.search-toggle').click(function(){
				var id = jQuery(this).attr('id');
				if(id=='place'){
					jQuery('#search-place-normal').hide('slow');
					jQuery('#search-place-basic').show('slow');
				}else if(id=='friends'){
					jQuery('#search-friends-normal').hide('slow');
					jQuery('#search-friends-basic').show('slow');
				}
			});

			//basic_place_selector
			jQuery('input[type=radio].basic_place_group').live( "change", function() {
				var place_id = jQuery(this).val();
				jQuery('#facebook_place_id').val(place_id);
				console.log(place_id);
			});

			//basic_friends_selector
			jQuery('input[type=checkbox].basic_friend_item').live( "change", function() {
				var friend_id = jQuery(this).val();		
				var friend_name = jQuery(this).attr('data-friend_name');
				       
				//in or out
				if(jQuery(this).prop('checked', jQuery(this).checked)){
					basic_selected_friends_list.push(friend_id);
					basic_selected_friends_name_list.push(friend_name);
				}else{
					var rem_ind;
					for(x in basic_selected_friends_list){
						if(basic_selected_friends_list[x] == friend_id){
							rem_ind = x;
						}
					}
					basic_selected_friends_list.splice(rem_ind, 1);
					basic_selected_friends_name_list.splice(rem_ind, 1);
				}
				
				jQuery('input[name=tagged_user_facebook_ids]').val(basic_selected_friends_list.join());
				jQuery('#selected-friends-list').html(basic_selected_friends_name_list.join());
				//console.log(basic_selected_friends_list.join());
			});

			jQuery('.search_basic_submit').click(function(){
				var id = jQuery(this).attr('data-id');
				place_search_result = [];
				if(id=='place'){
					search_data = jQuery('#search_place_basic').val();
					facebookPlaceSearch(search_data);

					var basic_place_search_result = '';
					var intervalId = setInterval(
						//wait for result flag : valid_place_search_result
						function(){	
							if(valid_place_search_result){
								//console.log(place_search_result);
								if(place_search_result.length == 0){
									jQuery('#search_place_basic_result').html('search not found');
								}else{
									basic_place_search_result = '<ul>';
									for(x in place_search_result){
										basic_place_search_result += '<li style="list-style:none;"><input type="radio" class="basic_place_group" name="place_group" value="'+place_search_result[x].place_id+'">'+place_search_result[x].label+"</li>";
									}
									basic_place_search_result += '</ul>';
									
									jQuery('#loading-box').hide();
									jQuery('#search_place_basic_result').html(basic_place_search_result);	
								}
								valid_place_search_result = false;
						   		clearInterval(intervalId);
						   	}	
						}
					,100);
				

				}else if(id=='friends'){
					search_result = '';
					search_data = jQuery('#search_friends_basic').val();
					jQuery('#loading-box').show();

					//TO-DO
					//do basic friends search
					//wait for result and use localFriendListSearch

					if(!valid_friends_list_result){
						facebookFriendsGet();
						//first time of friends getting
						var intervalId = setInterval(
							//wait for result flag : valid_place_search_result
							function(){	
								if(valid_friends_list_result){
									//console.log(friends_list_result);
									if(friends_list_result.length == 0){
										jQuery('#search_friends_basic_result').html('search not found');
										valid_friends_list_result = false
									}else{
										staticFriendListShow(search_data);
										//console.log('show after init');
									}
							   		clearInterval(intervalId);
							   	}	
							}
						,100);
					}else{
						staticFriendListShow(search_data);
						//console.log('show normal');
					}

					

				}
			});		
		});

		setInterval(
			//wait for result flag : valid_place_search_result
			function(){	
				if(valid_place_search_result){
			   		//console.log(place_search_result);
			   		jQuery( "#search_name" ).autocomplete( "option", "source", place_search_result);
			   	}	
			}
		,100);

			
		function noenter() {
  			return !(window.event && window.event.keyCode == 13); 
  		}

	</script>
</html>