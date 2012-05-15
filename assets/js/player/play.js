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
          $('.played-apps-list').masonry({
            itemSelector: '.played-app-item',
            columnWidth: 180
          });
          $('.played-app-item').hover(
            function show() {
              $('.played-app-detail', this).stop(true, true).slideDown(200);
              console.log('show');
            },function hide() {
              $('.played-app-detail', this).stop(true, true).slideUp(200);
            }
          );
        }
        
        var available_apps = data.available_apps;
        if(available_apps.length > 0) {
          var availableAppsTemplate = _.template($('#app-item-template').html());
          $('#all-apps').replaceWith(availableAppsTemplate({
            available_apps: available_apps
          }));
          $('.all-apps-list').masonry({
            itemSelector: '.app-item',
            columnWidth: 250
          });
          appItemHover();
        }
      } else {
        //guest
        var all_apps = data.available_apps;
        if(all_apps.length > 0) {
          var allAppsTemplate = _.template($('#app-item-template').html());
          $('#all-apps').replaceWith(allAppsTemplate({
            available_apps: all_apps
          }));
          appItemHover();
        }
      }
    }
  });
}

checkFBConnected(function(id) {
  user_facebook_id = id;
  get_user_data(id);
});

function appItemHover() {
  $('.app-item').hover(
    function show() {
      $('.app-detail', this).stop(true, true).slideDown(200);
    },function hide() {
      $('.app-detail', this).stop(true, true).slideUp(200);
    }
  );
}