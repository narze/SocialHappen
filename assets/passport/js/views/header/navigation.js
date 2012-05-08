define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/header/navigation.html' 
], function($, _, Backbone, headerMenuTemplate){
  var HeaderNavigationView = Backbone.View.extend({
    headerMenuTemplate: _.template(headerMenuTemplate),
    el: '#header',
    intialize: function () {
      
    },
    render: function () {
      $(this.el).html(this.headerMenuTemplate({
        baseUrl: window.Passport.BASE_URL,
        user: this.options.userModel
      }));
    },
    events: {

    }
  })

  return HeaderNavigationView;
});
