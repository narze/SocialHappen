define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/profile.html'
], function($, _, Backbone, profileTemplate){
  var ProfilePane = Backbone.View.extend({
    profileTemplate: _.template(profileTemplate),
    
    initialize: function(){
      _.bindAll(this);
      this.options.userModel.bind('change', this.render);
    },
    
    render: function () {
      $(this.el).html(this.profileTemplate({
        user: this.options.userModel.toJSON()
      }));
      
      return this;
    }
  });
  return ProfilePane;
});
