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

      var criteria = this.model.get('criteria');
      if(criteria &&
        criteria.length > 0 &&
        criteria[0].action_data &&
        criteria[0].action_data.action_id){
        if(criteria[0].action_data.action_id == 201){
          data.action_type = 'qr';
        }else if(criteria[0].action_data.action_id == 204){
          data.action_type = 'walkin';
        }else if(criteria[0].action_data.action_id == 203){
          data.action_type = 'checkin';
        }else if(criteria[0].action_data.action_id == 202){
          data.action_type = 'feedback';
        }else{
          data.action_type = null;
        }
      }else{
        data.action_type = null;
      }

      var rewardItems = this.model.get('reward_items');
      if(rewardItems &&
        rewardItems.length > 0 &&
        rewardItems[0].is_points_reward &&
        rewardItems[0].value){
        data.point = rewardItems[0].value
      }else{
        data.point = null;
      }

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
