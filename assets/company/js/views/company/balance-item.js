define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/balance-item.html',
  'timeago'
], function($, _, Backbone, balanceItemTemplate, timeago){
  var BalanceItem = Backbone.View.extend({
    tagName: 'li',
    balanceItemTemplate: _.template(balanceItemTemplate),
    initialize: function(){
      _.bindAll(this);
    },
    render: function () {
      if(this.model.get('message')){
        var data = this.model.toJSON();
        console.log(data);
        data.timeago = $.timeago(new Date(data.timestamp*1000));

        $(this.el).html(this.balanceItemTemplate(data));
      }
      return this;
    }
  });
  return BalanceItem;
});
