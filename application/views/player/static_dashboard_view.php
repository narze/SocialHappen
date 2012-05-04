<?php echo $header; ?>

	<div class="container">
  	<div class="row">
      <div class="span12">
        <div class="user-data well">
          
        </div>
      </div>
      <div class="span12">
        <div class="page-header">
          <h1>All apps you've played</h1>
        </div>
        <div class="played-app-container">
          <ul class="played-apps-list">
            
          </ul>
        </div>
        
      </div>
      <div class="span12">
        <div class="page-header">
          <h1>Explore more SocialHappen apps
            <small>play apps and collect points</small>
          </h1>
        </div>
        <ul class="all-apps-list">
        
        </ul>
      </div>
    </div>
  </div>

  <script type="text/template" id="user-data-template">
    <div class="row">
      <div class="span3">
        <img src="<%= picture %>" class="user-picture" />
      </div>
      <div class="span8">
        <h2 class="user-name">
          <%= name %>
        </h2>
        <p class="user-point">User Point: <%= point %></p>
      </div>
    </div>
  </script>
  <script type="text/template" id="app-played-item-template">
    <li class="played-app">
      <div class="played-app">
        <a href="<%= url %>" title="Play" alt="Play"><img src="<%= picture %>" class="app-photo"/></a>
        <h3 class="app-name"><%= name %></h3>
      </div>
    </li>
  </script>
  <script type="text/template" id="app-item-template">
    <li class="app-item">
      <div class="app-item">
        <div class="app-photo">
          <a href="<%= url %>" title="Play" alt="Play"><img src="<%= picture %>" class="app-photo"/></a>
        </div>
        <div class="app-detail">
          <h3 class="app-name"><%= name %></h3>
          <p class="description"><%= description %></p>
          <p>
            <a href="<%= url %>" title="Play" alt="Play" class="btn btn-success">Play</a>
          </p>
        </div>
        <div class="clear"></div>
      </div>
    </li>
  </script>

	<script>
		var user_facebook_id = 0;
		var fb_loaded = false;

		var facebook_image = '';
		var facebook_name = '';
		var facebook_email = '';

		function allow_facebook_login(){
			fb_loaded = true;
		}

		function fbcallback(data){
			user_facebook_id = data.id;
			get_user_data(data.id);
		}

		function get_user_data(){
			jQuery.ajax({
				url: '<?php echo base_url(); ?>player/static_get_user_data',
				type: "POST",
				data: {
					user_facebook_id: user_facebook_id
				},
				dataType: "json",
				success:function(data){
					console.log('get_user_data', data);
					$('#progress_bar').hide();
					
					var sh_user = data.sh_user;
					var userDataTemplate = _.template($('#user-data-template').html());
					
					jQuery('.user-data').html(userDataTemplate({
					  picture: sh_user.user_image + '?type=large',
					  name: sh_user.user_first_name + ' ' + sh_user.user_last_name,
					  point: data.user_score
					}));
          
          var played_apps = data.played_apps;
          var playedAppTemplate = _.template($('#app-played-item-template').html());
          _.each(played_apps, function(app){
            jQuery('.played-apps-list').append(playedAppTemplate({
              picture: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash2/373027_189828287722179_1658533100_n.jpg', //app.app_icon,
              name: app.app_name,
              url: app.app_url
            }));
          });

          if(!played_apps.length) {
            $('.played-app-container').parent('.span12').hide();
          }
          
          var available_apps = data.available_apps;
          var appItemTemplate = _.template($('#app-item-template').html());
          _.each(available_apps, function(app){
            jQuery('.all-apps-list').append(appItemTemplate({
              picture: 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash2/373027_189828287722179_1658533100_n.jpg', //app.app_icon,
              name: app.app_name,
              description: app.app_description,
              url: app.app_url
            }));
          });
          
          if(!available_apps.length) {
            $('.all-apps-list').parent('.span12').hide();
          }
          
				}
			});
		}

		jQuery(document).ready(function(){
			
		});
	</script>
</body>
</html>