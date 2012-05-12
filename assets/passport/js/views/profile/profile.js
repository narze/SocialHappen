define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/profile.html',
  'timeago'
], function($, _, Backbone, profileTemplate, timeago){
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
