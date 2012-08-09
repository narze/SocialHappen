define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/profile.html',
  'timeago',
  'sandbox'
], function($, _, Backbone, profileTemplate, timeago, sandbox){
  var ProfilePane = Backbone.View.extend({
    profileTemplate: _.template(profileTemplate),

    initialize: function(){
      _.bindAll(this);
      sandbox.models.userModel.bind('change', this.render);
    },

    render: function () {
      $(this.el).html(this.profileTemplate({
        user: sandbox.models.userModel.toJSON()
      }));

      return this;
    }
  });
  return ProfilePane;
});
