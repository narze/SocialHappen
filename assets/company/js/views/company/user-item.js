define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/user-item.html',
  'timeago'
], function($, _, Backbone, userItemTemplate, timeago){
  var CompanyUserItem = Backbone.View.extend({
    className: 'user-item row-fluid',
    userItemTemplate: _.template(userItemTemplate),
    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      var data = this.model.toJSON();
      $(this.el).html(this.userItemTemplate(data));
      return this;
    }
  });
  return CompanyUserItem;
});
