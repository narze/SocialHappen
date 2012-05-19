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
      if(data.user) { //If is user, show user's played apps
        // console.log('get_user_data', data);

        var user = data.user;

        var userDataTemplate = _.template($('#user-data-template').html());

        $('#user-data').replaceWith(userDataTemplate({
          picture: user.user_image + '?type=large',
          name: user.user_first_name + ' ' + user.user_last_name,
          point: data.user_score
        }));

        //Show played apps
        var played_apps = data.played_apps;
        if(played_apps.length > 0) {
          var playedAppTemplate = _.template($('#app-played-item-template').html());
          _.each(played_apps, function(app, i){
            played_apps[i].picture = 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash2/373027_189828287722179_1658533100_n.jpg'; //app.app_icon,
          });
          $('#played-apps').replaceWith(playedAppTemplate({
            played_apps: played_apps
          }));
          playedAppItemHover();
          $('.played-apps-list').imagesLoaded(function(){
            $(this).masonry({
              itemSelector: '.played-app-item',
              columnWidth: 180
            });
          });
        }
      }

      //Show all apps
      var available_apps = data.available_apps;
      if(available_apps.length > 0) {
        var availableAppsTemplate = _.template($('#app-item-template').html());
        $('#all-apps').replaceWith(availableAppsTemplate({
          available_apps: available_apps
        }));
        appItemHover();
        $('.all-apps-list').imagesLoaded(function(){
          $(this).masonry({
            itemSelector: '.app-item',
            columnWidth: 250
          });
        });
      }
    }
  });

  function appItemHover() {
    $('.app-item').hover(
      function show() {
        $('.play-button', this).stop(true, true).fadeIn(100);
      },function hide() {
        $('.play-button', this).stop(true, true).fadeOut(100);
      }
    );
  }

  function playedAppItemHover() {
    $('.played-app-item').hover(
      function show() {
        $('.play-button', this).stop(true, true).fadeIn(100);
      },function hide() {
        $('.play-button', this).stop(true, true).fadeOut(100);
      }
    );
  }
}

checkFBConnected(function(id) {
  user_facebook_id = id;
  get_user_data(id);
});
