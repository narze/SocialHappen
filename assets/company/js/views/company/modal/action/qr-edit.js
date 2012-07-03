define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/QRActionTemplate.html'
], function($, _, Backbone, QRActionTemplate){
  var QREditView = Backbone.View.extend({
    QRActionTemplate: _.template(QRActionTemplate),
    tagName: 'li',
    formEl: null,
    events: {
      'click .edit-action': 'showEdit',
      'click .remove-action': 'remove',
      'click button.remove': 'removeAction'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.QRActionTemplate(this.options.action));
      return this;
    },
    
    showEdit: function(){
      this.formEl = $('.modal', this.el).appendTo('#action-modal').modal('show');
      $('button.save', this.formEl).click(this.saveEdit);
      $('button.cancel', this.formEl).click(this.cancelEdit);
    },
    
    saveEdit: function(e){
      e.preventDefault();
      
      var dataId = this.options.action.action_data_id;
      
      this.options.action.name = $('input.name', this.formEl).val();
      this.options.action.action_data.data.todo_message = $('textarea.todo_message', this.formEl).val();
      this.options.action.action_data.data.done_message = $('textarea.done_message', this.formEl).val();
      
      var criteria = this.model.get('criteria');
      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },
    
    removeAction: function(e){
      e.preventDefault();
      
      var dataId = this.options.action.action_data_id;
      var criteria = this.model.get('criteria');
      
      var target = $(e.currentTarget).parent().parent().parent().parent();
      var index = target.index();
            
      delete criteria[index];
      criteria = _.compact(criteria);
      
      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
    
    
  });
  return QREditView;
});
