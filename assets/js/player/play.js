var user_facebook_id = 0;
var fb_loaded = false;

var facebook_image = '';
var facebook_name = '';
var facebook_email = '';

function allow_facebook_login(){
  fb_loaded = true;
}

function get_user_data(){
  $.ajax({
    url: base_url + 'apiv3/user_play_data',
    type: "POST",
    dataType: "json",
    success:function(data){
      if(data.user) {
        console.log('get_user_data', data);
        
        var user = data.user;

        var userDataTemplate = _.template($('#user-data-template').html());
        
        $('#user-data').replaceWith(userDataTemplate({
          picture: user.user_image + '?type=large',
          name: user.user_first_name + ' ' + user.user_last_name,
          point: data.user_score
        }));
        
        var played_apps = data.played_apps;
        if(played_apps.length > 0) {
          var playedAppTemplate = _.template($('#app-played-item-template').html());
          _.each(played_apps, function(app, i){
            played_apps[i].picture = 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash2/373027_189828287722179_1658533100_n.jpg'; //app.app_icon,
          });
          $('#played-apps').replaceWith(playedAppTemplate({
            played_apps: played_apps
          }));
          $('.played-app-list').masonry({
            itemSelector: '.played-app',
            columnWidth: 200

          });
        }
        
        var available_apps = data.available_apps;
        if(available_apps.length > 0) {
          var availableAppsTemplate = _.template($('#app-item-template').html());
          _.each(available_apps, function(app, i){
            available_apps[i].app_image = 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash2/373027_189828287722179_1658533100_n.jpg'; //app.app_icon,
          });
          $('#all-apps').replaceWith(availableAppsTemplate({
            available_apps: available_apps
          }));
          $('.all-apps-list').masonry({
            itemSelector: '.app-item',
            columnWidth: 270

          });
        }
      } else {
        //guest
        var all_apps = data.available_apps;
        if(all_apps.length > 0) {
          var allAppsTemplate = _.template($('#app-item-template').html());
          _.each(all_apps, function(app, i){
            all_apps[i].app_image = 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash2/373027_189828287722179_1658533100_n.jpg'; //app.app_icon,
          });
          $('#all-apps').replaceWith(allAppsTemplate({
            available_apps: all_apps
          }));
        }
      }
    }
  });
}

checkFBConnected(function(id) {
  user_facebook_id = id;
  get_user_data(id);
});