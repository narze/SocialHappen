define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/feedbackAddTemplate.html',
  'text!templates/company/modal/action/feedbackActionTemplate.html'
], function($, _, Backbone, feedbackTemplate, feedbackActionTemplate){
  var FeedbackAddView = Backbone.View.extend({
    feedbackTemplate: _.template(feedbackTemplate),
    feedbackActionTemplate: _.template(feedbackActionTemplate),
    tagName: 'li',
    
    events: {
      'click .edit-action': 'showEdit',
      'click .remove-action': 'remove',
      'click button.save': 'saveEdit',
      'click button.cancel': 'cancelEdit'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.feedbackActionTemplate(this.options.action));
      $('#action-modal').html(this.feedbackTemplate(this.options.action));
      return this;
    },
    
    showEdit: function(){
      $('#action-modal').modal('show');
    },
    
    saveEdit: function(e){
      e.preventDefault();
      
      this.options.action = {
        query: {
          action_id: 202
        },
        count: 1
      };
      this.options.action.name = $('input.name', this.el).val();
      this.options.action.action_data = {
        data: {},
        action_id: 202
      };
      this.options.action.action_data.data.feedback_welcome_message = $('textarea.feedback_welcome_message', this.el).val();
      this.options.action.action_data.data.feedback_question_message = $('textarea.feedback_question_message', this.el).val();
      this.options.action.action_data.data.feedback_vote_message = $('textarea.feedback_vote_message', this.el).val();
      this.options.action.action_data.data.feedback_thankyou_message = $('textarea.feedback_thankyou_message', this.el).val();

      var criteria = this.model.get('criteria');
      
      criteria.push(this.options.action);
      
      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },
    
    cancelEdit: function(e){
      e.preventDefault();
      $('div.edit', this.el).hide();
      this.model.trigger('change');
      this.options.vent.trigger(this.options.triggerModal, this.model);
      this.remove();
    },

    remove: function(e) {
      e.preventDefault();
      this.$el.remove();
      $('#action-modal').empty();
    }
  });
  return FeedbackAddView;
});
