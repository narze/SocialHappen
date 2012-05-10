var user_facebook_id = 0;
var fb_loaded = false;

var facebook_image = '';
var facebook_name = '';
var facebook_email = '';

function allow_facebook_login(){
  fb_loaded = true;
}

function get_user_data(){
  jQuery.ajax({
    url: base_url + 'player/static_get_user_data',
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

checkFBConnected(function(id) {
  user_facebook_id = id;
  get_user_data(id);
});