define([
  'jquery',
  'underscore',
  'backbone',
  'bootstrap',
  'text!templates/header/navigation.html',
  'text!templates/header/bar-notification.html'
], function($, _, Backbone, bootstrap, headerMenuTemplate, barNotificationTemplate){
  var HeaderNavigationView = Backbone.View.extend({
    headerMenuTemplate: _.template(headerMenuTemplate),
    barNotificationTemplate: _.template(barNotificationTemplate),
    el: '#header',
    events: {

    },
    initialize: function () {
      _.bindAll(this);
      this.options.currentUserModel.bind('change', this.render);
    },
    render: function () {
      $(this.el).html(this.headerMenuTemplate({
        baseUrl: window.Passport.BASE_URL,
        user: this.options.currentUserModel.toJSON()
      }));
      $('div#header .passport').addClass('active');
      
      var barNotificationTemplate = this.barNotificationTemplate;
      
      $.ajax({
        url: window.Passport.BASE_URL + 'apiv3/notifications/' + window.Passport.userId,
        type: "POST",
        dataType: "json",
        success: function(data) {
          console.log('notification', data, $('.bar-notification'));

          $('.bar-notification').replaceWith(barNotificationTemplate({
            baseUrl: window.Passport.BASE_URL,
            notifications: {
              count: data.count,
              list: data.items
            }
          }));
          $('.no-notification').hide();
        }
      });
      
      return this;
    }
  })

  return HeaderNavigationView;
});
