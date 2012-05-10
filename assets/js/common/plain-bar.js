$(function() {
  $.ajax({
    url: base_url + 'apiv3/user',
    type: "POST",
    dataType: "json",
    success:function(data){
      $('#progress_bar').hide();
      
      var barMenuTemplate = _.template($('#bar-menu-template').html());
      $('.bar-menu').html(barMenuTemplate({
        user: {
          id: data.user_id
        },
        baseUrl: base_url
      })).children('.play').addClass('active');
      
      
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


      var barNotificationTemplate = _.template($('#bar-notification-template').html());
      $('.bar-notification').replaceWith(barNotificationTemplate({
        baseUrl: base_url
      }));
    }
  });
});