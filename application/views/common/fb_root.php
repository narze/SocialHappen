<div id="fb-root"></div>
<script>
  var fbData = {}

  /**
   * Check if user is connected to facebook
   * Loop until connected, then callback
   */
  function checkFBConnected(callback) {
    if(typeof callback === 'function') {
      if(!fbData.fbConnected) {
        setTimeout(function() {
          checkFBConnected(callback);
        }, 50);
      } else {
        callback(true);
      }
    }
  }

  /**
   * Bind fbLogin function that calls FB.login()
   * When performed login, callback will be called with status boolean
   */
  function bindFBLogin() {
    if(fbData.fbEnsureInit) {
      window.fbLogin = function (callback) {
        FB.login(function(response) {
          if(typeof callback === 'function') {
            if (response.status === 'connected') {
              callback(true);
            } else {
              callback(false);
            }
          }
        }, {scope:'<?php echo $facebook_app_scope;?>'});
      };
    }
  }

  window.fbAsyncInit = function() {
    FB.init({appId: '<?php echo $facebook_app_id; ?>', 
      channelURL: '<?php echo $facebook_channel_url;?>', 
      status: true, 
      cookie: true,
      xfbml: true,
      oauth: true
    });

    fbData.fbEnsureInit = true;

    FB.getLoginStatus(function(response) {
      if (response.status === 'connected') {
        bindFBLogin();
        fbData.fbConnected = true;
        // var uid = response.authResponse.userID;
        // var accessToken = response.authResponse.accessToken;
      // } else if (response.status === 'not_authorized') {
      //   // the user is logged in to Facebook, 
      //   // but has not authenticated your app
      //   bindFBLogin();
      //   fbData.fbConnected = false;
      } else {
        // the user isn't logged in to Facebook.
        bindFBLogin();
        fbData.fbConnected = false;
      }
    });

    FB.Event.subscribe('auth.statusChange', function(response) {
      if(response.status === 'connected') {
        fbData.fbConnected = true;
      } else {
        bindFBLogin();
        fbData.fbConnected = false;
      }
    });    
  };

  (function(d){
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
  }(document));
</script>