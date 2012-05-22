define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/edit.html'
], function($, _, Backbone, editTemplate){
  var EditModalView = Backbone.View.extend({
    editTemplate: _.template(editTemplate),
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showEditModal', this.show);
    },
    
    render: function () {
      var data = this.model.toJSON();
      $(this.el).html(this.editTemplate(data));
      
      return this;
    },
    
    show: function(model){
      this.model = model;
      console.log('show edit modal:', model.toJSON());
      this.render();
      
      this.$el.modal('show');

    }
  });
  return EditModalView;
});
