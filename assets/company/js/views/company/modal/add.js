define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/modal/add.html',
  'views/company/modal/action/feedback-edit',
  'views/company/modal/action/feedback-add',
  'views/company/modal/action/qr-edit',
  'views/company/modal/action/qr-add',
  'views/company/modal/action/checkin-edit',
  'views/company/modal/action/checkin-add',
  'jqueryui'
], function($, _, Backbone, ChallengeModel, addTemplate, FeedbackEditView,
   FeedbackAddView, QREditView, QRAddView, CheckinEditView, CheckinAddView,
   jqueryui){
  var EditModalView = Backbone.View.extend({
    addTemplate: _.template(addTemplate),
    
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
      'click button.create-challenge': 'createChallenge'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showAddModal', this.show);
    },
    
    render: function () {
      console.log('render modal');
      
      var data = this.model.toJSON();
      $(this.el).html(this.addTemplate(data));
      
      var self = this;
       
      $('#add_challenge_start').datetimepicker({
        onClose : function(dateText, inst) {
          var date = $('#add_challenge_start').datetimepicker('getDate');
          self.model.set({
            start_date: date.getTime()/1000
          });
        }
      });
      $('#add_challenge_end').datetimepicker({
        onClose : function(dateText, inst) {
          var date = $('#add_challenge_end').datetimepicker('getDate');
          self.model.set({
            end_date: date.getTime()/1000
          });
        }
      });
      
      var startDate = this.model.get('start_date');
      if(startDate){
        startDate *= 1000;
        $('#add_challenge_start').datetimepicker('setDate', (new Date(startDate)));
      }
      
      var endDate = this.model.get('end_date');
      if(endDate){
        endDate *= 1000;
        $('#add_challenge_end').datetimepicker('setDate', (new Date(endDate)));
      }
      
      
      return this;
    },
    
    show: function(model){
      // if(!model){
        // this.model = new ChallengeModel({});
      // }else{
        this.model = model;
      // }
      console.log('show add modal:', this.model.toJSON());
      this.render();
      
      var criteria = this.model.get('criteria');
      
      _.each(criteria, function(action){
        var type = action.query.action_id;
        if(type == 202){
          var feedbackEditView = new FeedbackEditView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showAddModal'
          });
          
          $('ul.criteria-list', this.el).append(feedbackEditView.render().el);
        }else if(type == 201){
          var qrEditView = new QREditView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showAddModal'
          });
          
          $('ul.criteria-list', this.el).append(qrEditView.render().el);
        }else if(type == 203){
          var checkinEditView = new CheckinEditView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showAddModal'
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
      
      $('h3.edit-name', this.$el).show();
      $('div.edit-name', this.$el).hide();
      
      this.options.vent.trigger('showAddModal', this.model);
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
      
      this.options.vent.trigger('showAddModal', this.model);
    },
    
    showEditImage: function(){
      $('div.edit-image', this.el).show();
    },
    
    saveEditImage: function(){
      $('div.edit-image', this.el).hide();
      
      var detail = this.model.get('detail');
      detail.image = $('input.challenge-image', this.el).val();
      
      this.model.set('detail', detail).trigger('change');
            
      this.options.vent.trigger('showAddModal', this.model);
    },
    
    addFeedback: function(e){
      e.preventDefault();
      console.log('show add feedback');
      
      var feedbackAddView = new FeedbackAddView({
        model: this.model,
        vent: this.options.vent,
        triggerModal: 'showAddModal'
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
        triggerModal: 'showAddModal'
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
        triggerModal: 'showAddModal'
      });
      
      $('ul.criteria-list', this.el).prepend(checkinAddView.render().el);
      
      checkinAddView.showEdit();
    },
    
    createChallenge: function(){
      console.log('create challenge!');
      this.model.set('company_id', window.Company.companyId);
      this.options.challengesCollection.create(this.model);
      
      this.$el.modal('hide');
    }
  });
  return EditModalView;
});
