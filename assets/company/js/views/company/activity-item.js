define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/activity-item.html',
  'timeago'
], function($, _, Backbone, activityItemTemplate, timeago){
  var ActivityItem = Backbone.View.extend({
    tagName: 'li',
    activityItemTemplate: _.template(activityItemTemplate),
    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      if(this.model.get('message')){
        var data = this.model.toJSON();
        data.timeago = $.timeago(new Date(data.timestamp*1000));
        $(this.el).html(this.activityItemTemplate(data));
      }
      return this;
    }
  });
  return ActivityItem;
});
