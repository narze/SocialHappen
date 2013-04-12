define([
  'jquery',
  'underscore',
  'backbone',
  'views/company/modal/reward/reward-form',
  'text!templates/company/modal/reward/rewardTemplate.html'
], function($, _, Backbone, RewardFormView, RewardItemTemplate){
  var RewardView = Backbone.View.extend({

    rewardItemTemplate: _.template(RewardItemTemplate),

    tagName: 'li',

    events: {
      'click .edit-reward': 'showEdit',
      'click .remove-reward': 'remove'
    },

    initialize: function(){
      _.bindAll(this);

      //Add reward into model
      if(this.options.add) {
        var reward_items = this.model.get('reward_items');

        reward_items.push(this.options.reward_item);

        this.model.set('reward_items', reward_items).trigger('change');

        if(this.options.save){
          var self = this;

          this.model.save({}, {
            success: function(){
              self.options.vent.trigger('showEditModal', self.model);
            }
          });
        }
      }
    },

    render: function () {
      $(this.el).html(this.rewardItemTemplate(this.options.reward_item));
      return this;
    },

    showEdit: function(){
      var rewardFormView = new RewardFormView({
        model: this.model,
        reward_item: this.options.reward_item,
        vent: this.options.vent,
        triggerModal: this.options.triggerModal,
        save: this.options.save
      });
      $('#action-modal').html(rewardFormView.render().el);
      $('#action-modal').modal('show');
    },

    remove: function(e) {
      e.preventDefault();
      var reward_items = this.model.get('reward_items');
      var removeIndex = $(e.currentTarget).parents('ul.reward-list > li').index();
      delete reward_items[removeIndex];
      reward_items = _.compact(reward_items);

      this.model.set('reward_items', reward_items).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
  });
  return RewardView;
});
