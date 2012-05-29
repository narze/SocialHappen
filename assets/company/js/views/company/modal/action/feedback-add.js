define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/feedbackAddTemplate.html'
], function($, _, Backbone, feedbackTemplate){
  var FeedbackAddView = Backbone.View.extend({
    feedbackTemplate: _.template(feedbackTemplate),
    tagName: 'li',
    
    events: {
      'click button.edit': 'showEdit',
      'click button.save': 'saveEdit',
      'click button.cancel': 'cancelEdit',
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.feedbackTemplate(this.options.action));
      
      return this;
    },
    
    showEdit: function(){
      $('div.edit', this.el).toggle();
    },
    
    saveEdit: function(e){
      e.preventDefault();
      $('div.edit', this.el).hide();
      
      this.options.action = {
        query: {
          action_id: 202
        }
      };
      this.options.action.name = $('textarea.name', this.el).val();
      this.options.action.action_data = {
        data: {},
        action_id: 202
      }
      this.options.action.action_data.data.feedback_welcome_message = $('textarea.feedback_welcome_message', this.el).val();
      this.options.action.action_data.data.feedback_question_message = $('textarea.feedback_question_message', this.el).val();
      this.options.action.action_data.data.feedback_vote_message = $('textarea.feedback_vote_message', this.el).val();
      this.options.action.action_data.data.feedback_thankyou_message = $('textarea.feedback_thankyou_message', this.el).val();
      
      var criteria = this.model.get('criteria');
      
      criteria.push(this.options.action);
      
      this.model.set('criteria', criteria).trigger('change');
      // this.model.save();
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },
    
    cancelEdit: function(e){
      e.preventDefault();
      $('div.edit', this.el).hide();
      this.model.trigger('change');
      this.options.vent.trigger(this.options.triggerModal, this.model);
      this.remove();
    }
    
    
  });
  return FeedbackAddView;
});
