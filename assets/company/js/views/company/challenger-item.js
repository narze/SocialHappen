define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/challenger-item.html'
], function($, _, Backbone, ChallengerItemTemplate){
  var ChallengerItem = Backbone.View.extend({

    challengerItemTemplate: _.template(ChallengerItemTemplate),

    events: {
    },

    initialize: function(){
    },

    render: function () {
      $(this.el).html(this.challengerItemTemplate(this.options.model));
      return this;
    }

  });
  return ChallengerItem;
});
