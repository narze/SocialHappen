define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/checkinEditTemplate.html'
], function($, _, Backbone, checkinEditTemplate){
  var CheckinFormView = Backbone.View.extend({
    checkinEditTemplate: _.template(checkinEditTemplate),

    events: {
      'click button.save': 'saveEdit',
      'click button.cancel': 'cancelEdit'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.checkinEditTemplate(this.options.action));
      return this;
    },
    
    showEdit: function(){
      $(this.el).modal('show');
    },

    saveEdit: function(e){
      e.preventDefault();
      
      this.options.action.name = $('input.name', this.el).val();
      this.options.action.action_data.data.checkin_facebook_place_id = $('input.checkin_facebook_place_id', this.el).val();
      this.options.action.action_data.data.checkin_facebook_place_name = $('input.checkin_facebook_place_name', this.el).val();
      this.options.action.action_data.data.checkin_min_friend_count = $('input.checkin_min_friend_count', this.el).val();
      this.options.action.action_data.data.checkin_welcome_message = $('textarea.checkin_welcome_message', this.el).val();
      this.options.action.action_data.data.checkin_challenge_message = $('textarea.checkin_challenge_message', this.el).val();
      this.options.action.action_data.data.checkin_thankyou_message = $('textarea.checkin_thankyou_message', this.el).val();
      
      var criteria = this.model.get('criteria');
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
  return CheckinFormView;
});