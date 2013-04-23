define([
  'jquery',
  'underscore',
  'backbone',
  'views/company/modal/action/feedback-form',
  'text!templates/company/modal/action/feedbackActionTemplate.html'
], function($, _, Backbone, FeedbackFormView, feedbackActionTemplate){
  var FeedbackAddView = Backbone.View.extend({
    feedbackActionTemplate: _.template(feedbackActionTemplate),
    tagName: 'li',

    events: {
      'click .edit-action': 'showEdit',
      'click .remove-action': 'remove'
    },

    initialize: function(){
      _.bindAll(this);

      //Add action into model
      if(this.options.add) {
        var criteria = this.model.get('criteria');

        criteria.push(this.options.action);

        this.model.set('criteria', criteria).trigger('change');
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
      $(this.el).html(this.feedbackActionTemplate(this.options.action));
      return this;
    },

    showEdit: function(){
      var feedbackFormView = new FeedbackFormView({
        model: this.model,
        action: this.options.action,
        vent: this.options.vent,
        triggerModal: this.options.triggerModal,
        save: this.options.save
      });
      $('#action-modal').html(feedbackFormView.render().el);
      $('#action-modal').modal('show');
    },

    remove: function(e) {
      e.preventDefault();
      var criteria = this.model.get('criteria');
      var removeIndex = $(e.currentTarget).parents('ul.criteria-list > li').index();
      delete criteria[removeIndex];
      criteria = _.compact(criteria);

      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
  });
  return FeedbackAddView;
});
