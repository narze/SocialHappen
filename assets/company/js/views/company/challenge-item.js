define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/challenge-item.html',
  'moment'
], function($, _, Backbone, challengeItemTemplate, moment){
  var ChallengeItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    challengeItemTemplate: _.template(challengeItemTemplate),
    events: {
      'click': 'showEdit'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
    },
    render: function () {
      console.log('render challenge item');
      var data = this.model.toJSON();
      data.baseUrl = window.Company.BASE_URL;

      var now = Math.floor(new Date().getTime()/1000);
      // console.log(data.detail.name, data.start_date, data.end_date, now);

      data.expired = data.end_date < now;
      data.notstart = data.start_date > now;

      $(this.el).html(this.challengeItemTemplate(data));
      return this;
    },

    showEdit: function(e){
      e.preventDefault();
      this.options.vent.trigger('showEditModal', this.model);
    }
  });
  return ChallengeItemView;
});
