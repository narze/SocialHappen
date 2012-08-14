require([
  'jquery',
  'underscore',
  'bootstrap',
  'moment'
], function($, _, bootstrap, moment) {
  window.fbLoginResult = function(success) {
    if(success) {
      window.location = base_url + 'login?next=' + window.location.href;
    }
  };

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
        // @TODO - better menu detection
        if(window.location.href.indexOf('/play/') !== -1) {
          $('.bar-menu>li.play').addClass('active');
        } else if (window.location.href.indexOf('/challenge/') !== -1) {
          $('.bar-menu>li.world').addClass('active');
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

        //Get notifications
        $.ajax({
          url: base_url + 'apiv3/notifications/' + data.user_id,
          type: "POST",
          dataType: "json",
          success: function(data) {
            //barNotificationTemplate uses moment.js
            var barNotificationTemplate = _.template($('#bar-notification-template').html());
            $('.bar-notification').replaceWith(barNotificationTemplate({
              baseUrl: base_url,
              notifications: {
                count: data.count,
                list: data.items
              },
              moment: moment
            }));
            $('.no-notification').hide();
          }
        });

        //Show company list
        if(data.companies) {
          var barCompanyListTemplate = _.template($('#bar-company-list-template').html());
          $('.bar-company-list').replaceWith(barCompanyListTemplate({
            companies: data.companies
          }));
        }

      } else {
        //guest
        var barLoginTemplate = _.template($('#bar-login-template').html());
        $('.nav.pull-right').html(barLoginTemplate({
          baseUrl: base_url
        }));
        $('.btn-login').click(function(e) {
          e.preventDefault();
          window.location = base_url + 'login?next=' + window.location.href;
          // $('#bar-login-modal').modal();
        });
      }
    }
  });

  //Now have nothing to return
  return {};
});