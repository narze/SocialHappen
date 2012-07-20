define([
  'jquery',
  'underscore',
  'backbone',
  'bootstrap',
  'moment',
  'text!templates/header/navigation.html',
  'text!templates/header/bar-notification.html',
  'text!templates/header/bar-company-list.html'
], function($, _, Backbone, bootstrap, moment, headerMenuTemplate, barNotificationTemplate, barCompanyListTemplate){
  var HeaderNavigationView = Backbone.View.extend({
    headerMenuTemplate: _.template(headerMenuTemplate),
    barNotificationTemplate: _.template(barNotificationTemplate),
    barCompanyListTemplate: _.template(barCompanyListTemplate),
    el: '#header',
    events: {
      'click .notification_list_bar>li>a' : 'seeNotification',
      'click .a-notification' : 'seeNotification'
    },
    initialize: function () {
      _.bindAll(this);
      this.options.currentUserModel.bind('change', this.render);
    },
    render: function () {
      $(this.el).html(this.headerMenuTemplate({
        baseUrl: window.World.BASE_URL,
        user: this.options.currentUserModel.toJSON()
      }));
      $('div#header .world').addClass('active');

      //Company list
      var companies = this.options.currentUserModel.get('companies');
      if(companies && companies.length) {
        var barCompanyListTemplate = this.barCompanyListTemplate;
        $('.bar-company-list').replaceWith(barCompanyListTemplate({
          companies: companies,
          base_url: window.World.BASE_URL
        }));
      }

      //Notifications
      var barNotificationTemplate = this.barNotificationTemplate;
      $.ajax({
        url: window.World.BASE_URL + 'apiv3/notifications/' + window.World.userId,
        type: "POST",
        dataType: "json",
        success: function(data) {
          console.log('notification', data, $('.bar-notification'));

          $('.bar-notification').replaceWith(barNotificationTemplate({
            baseUrl: window.World.BASE_URL,
            notifications: {
              count: data.count,
              list: data.items
            }
          }));
          $('.no-notification').hide();
        }
      });

      return this;
    },

    seeNotification: function(e){
      e.preventDefault();
    }
  });

  return HeaderNavigationView;
});
