define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/feedbackEditTemplate.html'
], function($, _, Backbone, feedbackTemplate){
  var FeedbackEditView = Backbone.View.extend({
    feedbackTemplate: _.template(feedbackTemplate),
    tagName: 'li',
    
    events: {
      'click button.edit': 'showEdit',
      'click button.save': 'saveEdit',
      'click button.remove': 'removeAction'
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
      
      var dataId = this.options.action.action_data_id;
      
      this.options.action.name = $('textarea.name', this.el).val();
      this.options.action.action_data.data.feedback_welcome_message = $('textarea.feedback_welcome_message', this.el).val();
      this.options.action.action_data.data.feedback_question_message = $('textarea.feedback_question_message', this.el).val();
      this.options.action.action_data.data.feedback_vote_message = $('textarea.feedback_vote_message', this.el).val();
      this.options.action.action_data.data.feedback_thankyou_message = $('textarea.feedback_thankyou_message', this.el).val();
      
      var criteria = this.model.get('criteria');
      this.model.set('criteria', criteria).trigger('change');
      this.model.save();
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },
    
    removeAction: function(e){
      e.preventDefault();
      
      var dataId = this.options.action.action_data_id;
      var criteria = this.model.get('criteria');
      
      criteria = _.reject(criteria, function(c){
        return c.action_data_id == dataId;
      }, this);
      
      this.model.set('criteria', criteria).trigger('change');
      this.model.save();
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
    
    
  });
  return FeedbackEditView;
});
