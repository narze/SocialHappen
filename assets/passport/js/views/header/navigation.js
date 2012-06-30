define([
  'jquery',
  'underscore',
  'backbone',
  'bootstrap',
  'timeago',
  'text!templates/header/navigation.html',
  'text!templates/header/bar-notification.html',
  'text!templates/header/bar-company-list.html'
], function($, _, Backbone, bootstrap, timeago, headerMenuTemplate, barNotificationTemplate, barCompanyListTemplate){
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
        baseUrl: window.Passport.BASE_URL,
        user: this.options.currentUserModel.toJSON()
      }));
      $('div#header .passport').addClass('active');
      
      //Company list
      var companies = this.options.currentUserModel.get('companies');
      if(companies && companies.length) {
        var barCompanyListTemplate = this.barCompanyListTemplate;
        $('.bar-company-list').replaceWith(barCompanyListTemplate({
          companies: companies,
          base_url: window.Passport.BASE_URL
        }));
      }

      //Notifications
      var barNotificationTemplate = this.barNotificationTemplate;
      $.ajax({
        url: window.Passport.BASE_URL + 'apiv3/notifications/' + window.Passport.userId,
        type: "POST",
        dataType: "json",
        success: function(data) {
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
    },
    
    seeNotification: function(e){
      e.preventDefault();
    }
  });

  return HeaderNavigationView;
});
