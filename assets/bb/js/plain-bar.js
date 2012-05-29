require([
  'jquery',
  'underscore',
  'bootstrap',
  'timeago'
], function($, _, bootstrap, timeago) {
  $.ajax({
    url: base_url + 'apiv3/user',
    type: "POST",
    dataType: "json",
    success:function(data){
      if(data.user_id) {
        var barMenuTemplate = _.template($('#bar-menu-template').html());
        $('.bar-menu').html(barMenuTemplate({
          user: {
            id: data.user_id
          },
          baseUrl: base_url
        }));
        if(window.location.href.indexOf('play')) {
          $('.bar-menu>li.play').addClass('active');
        }

        var barUserTemplate = _.template($('#bar-user-template').html());
        $('.bar-user').replaceWith(barUserTemplate({
          user: {
            user_image: data.user_image,
            user_first_name: data.user_first_name,
            user_last_name: data.user_last_name
          },
          baseUrl: base_url
        }));
        $('.btn-logout').click(function(e) {
          e.preventDefault();
          window.location = base_url + 'logout?redirect=' + window.location.href;
        });

        $.ajax({
          url: base_url + 'apiv3/notifications/' + data.user_id,
          type: "POST",
          dataType: "json",
          success: function(data) {
            //barNotificationTemplate uses jquery.timeago
            var barNotificationTemplate = _.template($('#bar-notification-template').html());
            $('.bar-notification').replaceWith(barNotificationTemplate({
              baseUrl: base_url,
              notifications: {
                count: data.count,
                list: data.items
              }
            }));
            $('.no-notification').hide();
          }
        });
      } else {
        //guest
        var barLoginTemplate = _.template($('#bar-login-template').html());
        $('.nav.pull-right').html(barLoginTemplate({
          baseUrl: base_url
        }));
        $('.btn-login').click(function(e) {
          e.preventDefault();
          window.location = base_url + 'login?next=' + window.location.href;
        });
      }
    }
  });
  
  //Now have nothing to return
  return {};
});