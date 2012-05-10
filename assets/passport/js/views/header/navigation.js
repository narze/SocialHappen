define([
  'jquery',
  'underscore',
  'backbone',
  'bootstrap',
  'text!templates/header/navigation.html' 
], function($, _, Backbone, bootstrap, headerMenuTemplate){
  var HeaderNavigationView = Backbone.View.extend({
    headerMenuTemplate: _.template(headerMenuTemplate),
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
      return this;
    }
  })

  return HeaderNavigationView;
});
