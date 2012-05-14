define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/world/page.html',
  'views/world/sidebar',
  'views/world/carousel',
  'views/world/challenge-list'
], function($, _, Backbone, pageTemplate, SidebarView, CarouselView, ChallengeListView){
  var ProfilePage = Backbone.View.extend({
    pageTemplate: _.template(pageTemplate),
    el: '#content',
    
    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      $(this.el).html(pageTemplate);
      $('div#header .play').addClass('active');
      
      var sidebarView = new SidebarView({
        el: $('#sidebar', this.el),
        userModel: this.options.userModel,
        vent: this.options.vent
      });
      sidebarView.render();
      
      var carouselView = new CarouselView({
        el: $('#carousel', this.el),
        userModel: this.options.userModel,
        vent: this.options.vent
      });
      carouselView.render();
      
      var challengeListView = new ChallengeListView({
        collection: this.options.challengesCollection,
        el: $('#challenge-list', this.el),
        vent: this.options.vent
      });
      
      challengeListView.render();
    }
  });
  return ProfilePage;
});
