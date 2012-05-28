define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/edit.html',
  'views/company/modal/action/feedback-edit',
  'views/company/modal/action/feedback-add',
  'views/company/modal/action/qr-edit',
  'views/company/modal/action/qr-add',
  'views/company/modal/action/checkin-edit',
  'views/company/modal/action/checkin-add'
], function($, _, Backbone, editTemplate, FeedbackEditView, FeedbackAddView,
  QREditView, QRAddView, CheckinEditView, CheckinAddView){
  var EditModalView = Backbone.View.extend({
    editTemplate: _.template(editTemplate),
    
    events: {
      'click h3.edit-name': 'showEditName',
      'click button.save-name': 'saveEditName',
      'click div.edit-description': 'showEditDescription',
      'click button.save-description': 'saveEditDescription',
      'click img.challenge-image, h6.edit-image': 'showEditImage',
      'click button.save-image': 'saveEditImage',
      'click a.add-feedback': 'addFeedback',
      'click a.add-qr': 'addQR',
      'click a.add-checkin': 'addCheckin',
      'click button.active-challenge': 'activeChallenge',
      'click button.deactive-challenge': 'deactiveChallenge'
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
      
      var criteria = this.model.get('criteria');
      
      _.each(criteria, function(action){
        var type = action.query.platform_action_id;
        if(type == 202){
          var feedbackEditView = new FeedbackEditView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showEditModal'
          });
          
          $('ul.criteria-list', this.el).append(feedbackEditView.render().el);
        }else if(type == 201){
          var qrEditView = new QREditView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showEditModal'
          });
          
          $('ul.criteria-list', this.el).append(qrEditView.render().el);
        }else if(type == 203){
          var checkinEditView = new CheckinEditView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showEditModal'
          });
          
          $('ul.criteria-list', this.el).append(checkinEditView.render().el);
        }
      }, this);
      
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
      this.model.save();
      
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
      this.model.save();
      
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
      this.model.save();
      
      this.options.vent.trigger('showEditModal', this.model);
    },
    
    activeChallenge: function(){
      this.model.set('active', true).trigger('change');
      this.model.save();
      this.options.vent.trigger('showEditModal', this.model);
    },
    
    deactiveChallenge: function(){
      this.model.set('active', false).trigger('change');
      this.model.save();
      this.options.vent.trigger('showEditModal', this.model);
    },

    addFeedback: function(e){
      e.preventDefault();
      console.log('show add feedback');
      
      var feedbackAddView = new FeedbackAddView({
        model: this.model,
        vent: this.options.vent,
        triggerModal: 'showEditModal'
      });
      
      $('ul.criteria-list', this.el).prepend(feedbackAddView.render().el);
      
      feedbackAddView.showEdit();
    },
    
    addQR: function(e){
      e.preventDefault();
      console.log('show add qr');
      
      var qrAddView = new QRAddView({
        model: this.model,
        vent: this.options.vent,
        triggerModal: 'showEditModal'
      });
      
      $('ul.criteria-list', this.el).prepend(qrAddView.render().el);
      
      qrAddView.showEdit();
    },
    
    addCheckin: function(e){
      e.preventDefault();
      console.log('show add checkin');
      
      var checkinAddView = new CheckinAddView({
        model: this.model,
        vent: this.options.vent,
        triggerModal: 'showEditModal'
      });
      
      $('ul.criteria-list', this.el).prepend(checkinAddView.render().el);
      
      checkinAddView.showEdit();
    }
  });
  return EditModalView;
});
