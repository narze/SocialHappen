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
		<div class="loading-div" style="height:36px;display:block;">
			<div id="loading-box" style="display:none;"><img src="<?php echo base_url().'assets/images/loading.gif'; ?>" /></div>
		</div>
		<form name="" action="<?php echo base_url('actions/checkin/add_user_data_checkin'); ?>" method="post">
			<div><?php echo $action_data['data']['checkin_challenge_message']; ?></div>
			<div>
				<p><a href="<?php echo current_url().'?code='.$action_data['hash'].'&basic_view=true'; ?>">Try basic view</a></p>
				<p>Do Check-In at : <?php echo $action_data['data']['checkin_facebook_place_name']; ?></p>
				<p>
					<div id="search-place-normal"> 
						autocomplete location search
						<input type="text" name="search_name" id="search_name" onkeypress="return noenter()" /><br />
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