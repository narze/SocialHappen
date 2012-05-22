define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/edit.html'
], function($, _, Backbone, editTemplate){
  var EditModalView = Backbone.View.extend({
    editTemplate: _.template(editTemplate),
    
    events: {
      'click h3.edit-name': 'showEditName',
      'click button.save-name': 'saveEditName',
      'click div.edit-description': 'showEditDescription',
      'click button.save-description': 'saveEditDescription',
      'click img.challenge-image': 'showEditImage',
      'click button.save-image': 'saveEditImage'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showEditModal', this.show);
    },
    
    render: function () {
      console.log('render modal');
      
      var data = this.model.toJSON();
      $(this.el).html(this.editTemplate(data));
      
      return this;
    },
    
    show: function(model){
      this.model = model;
      console.log('show edit modal:', model.toJSON());
      this.render();      
      this.$el.modal('show');

    },
    
    showEditName: function(){
      $('h3.edit-name', this.el).hide();
      $('div.edit-name', this.el).show();
    },
    
    saveEditName: function(){
      
      var detail = this.model.get('detail');
      detail.name = $('input.challenge-name', this.el).val();
      
      this.model.set('detail', detail).trigger('change');
      
      $('h3.edit-name', this.$el).show();
      $('div.edit-name', this.$el).hide();
      
      this.options.vent.trigger('showEditModal', this.model);
    },
    
    showEditDescription: function(){
      $('div.edit-description', this.el).hide();
      $('div.edit-description-field', this.el).show();
    },
    
    saveEditDescription: function(){
      
      var detail = this.model.get('detail');
      detail.description = $('textarea.challenge-description', this.el).val();
      
      this.model.set('detail', detail).trigger('change');
      
      $('div.edit-description', this.el).show();
      $('div.edit-description-field', this.el).hide();
      
      this.options.vent.trigger('showEditModal', this.model);
    },
    
    showEditImage: function(){
      $('div.edit-image', this.el).show();
    },
    
    saveEditImage: function(){
      $('div.edit-image', this.el).hide();
      
      var detail = this.model.get('detail');
      detail.image = $('input.challenge-image', this.el).val();
      
      this.model.set('detail', detail).trigger('change');
            
      this.options.vent.trigger('showEditModal', this.model);
    }
  });
  return EditModalView;
});
