define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/CheckinAddTemplate.html'
], function($, _, Backbone, CheckinTemplate){
  var CheckinAddView = Backbone.View.extend({
    CheckinTemplate: _.template(CheckinTemplate),
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
      $(this.el).html(this.CheckinTemplate(this.options.action));
      
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
          platform_action_id: 203
        }
      };
      this.options.action.name = $('textarea.name', this.el).val();
      this.options.action.action_data = {
        data: {},
        action_id: 203
      }
      this.options.action.action_data.data.checkin_facebook_place_id = $('input.checkin_facebook_place_id', this.el).val();
      this.options.action.action_data.data.checkin_welcome_message = $('textarea.checkin_welcome_message', this.el).val();
      this.options.action.action_data.data.checkin_challenge_message = $('textarea.checkin_challenge_message', this.el).val();
      this.options.action.action_data.data.checkin_thankyou_message = $('textarea.checkin_thankyou_message', this.el).val();
      
      var criteria = this.model.get('criteria');
      
      criteria.push(this.options.action);
      
      this.model.set('criteria', criteria).trigger('change');
      this.model.save();
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
  return CheckinAddView;
});
