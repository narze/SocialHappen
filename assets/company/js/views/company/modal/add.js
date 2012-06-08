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
      'click button.create-challenge': 'createChallenge',
      'click button.edit-reward': 'showEditReward',
      'click button.save-reward': 'saveEditReward',
      'click button.cancel-edit-reward': 'cancelEditReward',
      'click button.edit-score': 'showEditScore',
      'click button.save-score': 'saveEditScore'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showAddModal', this.show);
    },

    getRewards: function() {
      var self = this;
      console.log('getting rewards');
      //Get all rewards
      $.ajax({
        dataType: 'json',
        method: 'POST',
        url: window.Company.BASE_URL + 'apiv3/get_rewards_for_challenge',
        success: function(resp) {
          self.challengeRewards = resp.data;
          self.render();
        },
        error: function() {
          self.challengeRewards = {};
          self.render();
        }
      });
    },
    
    render: function () {
      console.log('render modal');
      
      if(typeof this.challengeRewards === 'undefined') {
        return this.getRewards();
      }
      
      var data = this.model.toJSON();
      data.challengeRewards = this.challengeRewards;
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
      
      var challengeRewards = this.challengeRewards;
      $('select.select-challenge-reward', this.el).change(function(e) {
        var challengeId = $(this).find('option:selected').data('challengeId');
        if(!challengeId) { return ;}
        var chosenChallenge = challengeRewards[challengeId];
        
        self.model.set('reward', {_id: chosenChallenge._id});

        $('input.reward-name', this.el).val(chosenChallenge.name);
        $('input.reward-image', this.el).val(chosenChallenge.image);
        $('input.reward-value', this.el).val(chosenChallenge.value);
        $('input.reward-status', this.el).val(chosenChallenge.status);
        $('input.reward-description', this.el).val(chosenChallenge.description);

        self.saveEditReward();
      });

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
      $('input.challenge-name', this.el).focus();
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
      $('textarea.challenge-description', this.el).focus();
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
      $('input.challenge-image', this.el).focus();
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

    showEditReward: function() {
      $('div.edit-reward', this.el).show();
      $('input.reward-name', this.el).focus();
    },

    saveEditReward: function(e) {
      $('div.edit-reward', this.el).hide();

      var reward = this.model.get('reward');
      reward.name = $('input.reward-name', this.el).val() || reward.name;
      reward.image = $('input.reward-image', this.el).val() || reward.image;
      reward.value = $('input.reward-value', this.el).val() || reward.value;
      reward.status = $('input.reward-status', this.el).val() || reward.status;
      reward.description = $('input.reward-description', this.el).val() || reward.description;
      
      this.model.set('reward', reward).trigger('change');
            
      this.options.vent.trigger('showAddModal', this.model);
    },

    cancelEditReward: function(e){
      e.preventDefault();
      $('div.edit-reward', this.el).hide();
      this.render();
    },

    showEditScore: function(){
      $('h3.edit-score', this.el).hide();
      $('div.edit-score', this.el).show();
      $('input.challenge-score', this.el).focus();
    },
    
    saveEditScore: function(){
      var score = $('input.challenge-score', this.el).val();
      var intRegex = /^\d+$/;
      if(!(intRegex.test(score))) {
        return;
      }

      this.model.set('score', score).trigger('change');
      
      $('h3.edit-score', this.$el).show();
      $('div.edit-score', this.$el).hide();
      
      this.options.vent.trigger('showAddModal', this.model);
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
