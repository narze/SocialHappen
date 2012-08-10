define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/profile/action-item.html',
  'timeago',
  'sandbox'
], function($, _, Backbone, actionItemTemplate, timeago, sandbox){
  var ActionItem = Backbone.View.extend({
    tagName: 'li',
    actionItemTemplate: _.template(actionItemTemplate),
    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      if(this.model.get('message')){
        var data = this.model.toJSON();
        if(!data.image) {
          data.image = window.Passport.BASE_URL + 'userimage/' + sandbox.userId
        }
        data.timeago = $.timeago(new Date(data.user_data.timestamp*1000));
        $(this.el).html(this.actionItemTemplate(data));
      }
      return this;
    }
  });
  return ActionItem;
});
