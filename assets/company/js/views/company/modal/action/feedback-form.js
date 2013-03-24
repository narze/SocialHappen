define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/feedbackEditTemplate.html'
], function($, _, Backbone, feedbackEditTemplate){
  var FeedbackFormView = Backbone.View.extend({
    feedbackEditTemplate: _.template(feedbackEditTemplate),

    events: {
      'click button.save': 'saveEdit',
      'click button.cancel': 'cancelEdit'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      $(this.el).html(this.feedbackEditTemplate(this.options.action));
      return this;
    },

    showEdit: function(){
      $(this.el).modal('show');
    },

    saveEdit: function(e){
      e.preventDefault();

      this.options.action.name = $('input.name', this.el).val();
      this.options.action.action_data.data.feedback_welcome_message = $('textarea.feedback_welcome_message', this.el).val();
      this.options.action.action_data.data.feedback_question_message = $('textarea.feedback_question_message', this.el).val();
      this.options.action.action_data.data.feedback_vote_message = $('textarea.feedback_vote_message', this.el).val();
      this.options.action.action_data.data.feedback_thankyou_message = $('textarea.feedback_thankyou_message', this.el).val();

      var criteria = this.model.get('criteria');

      if(this.options.save){
        for(var i = criteria.length - 1; i >= 0; i--) {
          var actionItem = criteria[i];

          if(actionItem.action_data_id == this.options.action.action_data_id){
            console.log('found action to save', criteria[i]);
            criteria[i] = _.clone(this.options.action);
            console.log('criteria to be saved', criteria);
            break;
          }
        };
      }

      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },

    cancelEdit: function(e){
      e.preventDefault();
      this.model.trigger('change');
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
  });
  return FeedbackFormView;
});
