define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/modal/add.html',
  'text!templates/company/modal/recipe.html',
  'text!templates/company/modal/addAction.html',
  'views/company/modal/action/feedback-add',
  'views/company/modal/action/qr-add',
  'views/company/modal/action/checkin-add',
  'jqueryui'
], function($, _, Backbone, ChallengeModel, addTemplate, recipeTemplate, addActionTemplate,
   FeedbackAddView, QRAddView, CheckinAddView,
   jqueryui){
  var EditModalView = Backbone.View.extend({
    addTemplate: _.template(addTemplate),
    addActionTemplate: _.template(addActionTemplate),
    recipeTemplate: _.template(recipeTemplate),
    
    events: {
      'click button.create-challenge': 'createChallenge',
      'click button.edit-reward': 'showEditReward',
      'click button.save-reward': 'saveEditReward',
      'click button.cancel-edit-reward': 'cancelEditReward',
      'click button.edit-score': 'showEditScore',
      'click button.save-score': 'saveEditScore',
      'change input.repeat-enable': 'toggleRepeat',
      'click button.save-repeat-interval': 'saveRepeat',
      'click div.view-repeat': 'showEditRepeat',
      'click .add-new-action': 'showAddNewActionModal'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showAddModal', this.show);
      this.options.vent.bind('showRecipeModal', this.showRecipeModal);
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
          
          var endDate = $('#add_challenge_end').datetimepicker('getDate');
          
          if(endDate && date && date >= endDate){
            alert('Start date must come before end date');
            var startDate = self.model.get('start_date');
            if(startDate){
              startDate *= 1000;
              $('#add_challenge_start').datetimepicker('setDate', (new Date(startDate)));
            }else{
              $('#add_challenge_start').datetimepicker('setDate', null);
            }
            return;
          }
          
          self.model.set({
            start_date: Math.floor(date.getTime()/1000)
          });
        }
      });
      $('#add_challenge_end').datetimepicker({
        onClose : function(dateText, inst) {
          var date = $('#add_challenge_end').datetimepicker('getDate');
          
          var startDate = $('#add_challenge_start').datetimepicker('getDate');
          
          if(date && startDate && startDate >= date){
            alert('End date must come after start date');
            var endDate = self.model.get('end_date');
            if(endDate){
              endDate *= 1000;
              $('#add_challenge_end').datetimepicker('setDate', (new Date(endDate)));
            }else{
              $('#add_challenge_end').datetimepicker('setDate', null);
            }
            return;
          }
          
          self.model.set({
            end_date: Math.floor(date.getTime()/1000)
          });
        }
      });
      
      this.model.set('start_date', Math.floor((new Date()).getTime()/1000));
      var startDate = this.model.get('start_date');
      if(startDate){
        startDate *= 1000;
        $('#add_challenge_start').datetimepicker('setDate', (new Date(startDate)));
      }
      
      this.model.set('end_date', Math.floor((new Date()).getTime()/1000 + 60*60*24*365));
      var endDate = this.model.get('end_date');
      if(endDate){
        endDate *= 1000;
        $('#add_challenge_end').datetimepicker('setDate', (new Date(endDate)));
      }
      
      $('select.select-challenge-reward', this.el).change(this.changeReward);

      return this;
    },
    
    show: function(model){
      this.model = model;
      console.log('show add modal:', this.model.toJSON());
      this.render();
      
      var criteria = this.model.get('criteria');
      
      _.each(criteria, function(action){
        var type = action.query.action_id;
        if(type == 202){
          var feedbackAddView = new FeedbackAddView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showAddModal'
          });
          
          $('ul.criteria-list', this.el).append(feedbackAddView.render().el);
        }else if(type == 201){
          var qrAddView = new QRAddView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showAddModal'
          });
          
          $('ul.criteria-list', this.el).append(qrAddView.render().el);
        }else if(type == 203){
          var checkinAddView = new CheckinAddView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showAddModal'
          });
          
          $('ul.criteria-list', this.el).append(checkinAddView.render().el);
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

    showAddNewActionModal: function(e) {
      var addActionModal = $('#add-action-modal');
      addActionModal.html(addActionTemplate).modal('show');
      var recipe = null;
      var self = this;

      //On recipe click
      $('.recipes button', addActionModal).click(function() {
        $('.recipes button', addActionModal).addClass('disabled');
        $(this).removeClass('disabled');
        recipe = $(this).data('recipe');
      });

      $('button.choose-recipe', addActionModal).click(function(e) {
        if(recipe === 'share') {
          // self.addShare(e);
        } else if(recipe === 'feedback') {
          self.addFeedback(e).showEdit();
        } else if(recipe === 'checkin') {
          self.addCheckin(e).showEdit();
        } else if(recipe === 'qr') {
          self.addQR(e).showEdit();
        }
      });
    },
    
    addFeedback: function(e){
      e.preventDefault();
      console.log('show add feedback');

      var feedbackDefaultAction = {
        query: {
          action_id: 202
        },
        count: 1,
        name: 'Feedback Action',
        action_data: {
          data: {
            feedback_welcome_message: 'Find and scan the QR code',
            feedback_question_message: 'Find and scan the QR code',
            feedback_vote_message: 'Find and scan the QR code',
            feedback_thankyou_message: 'Congratulations! You\'ve found and scanned the QR code'
          },
          action_id: 202
        }
      };

      var feedbackAddView = new FeedbackAddView({
        model: this.model,
        vent: this.options.vent,
        action: feedbackDefaultAction,
        triggerModal: 'showAddModal',
        add: true
      });
      
      $('ul.criteria-list', this.el).append(feedbackAddView.render().el);
      
      return feedbackAddView;
    },
    
    addQR: function(e){
      e.preventDefault();
      console.log('show add qr: ', this.model.toJSON());
      
      var qrDefaultAction = {
        query: {
          action_id: 201
        },
        count: 1,
        name: 'QR Action',
        action_data: {
          data: {
            todo_message: 'Find and scan the QR code',
            done_message: 'Congratulations! You\'ve found and scanned the QR code'
          },
          action_id: 201
        }
      };

      var qrAddView = new QRAddView({
        model: this.model,
        vent: this.options.vent,
        action: qrDefaultAction,
        triggerModal: 'showAddModal',
        add: true
      });


      $('ul.criteria-list', this.el).append(qrAddView.render().el);
      
      return qrAddView;
    },
    
    addCheckin: function(e){
      e.preventDefault();
      console.log('show add checkin');
      
      var checkinDefaultAction = {
        query: {
          action_id: 203
        },
        count: 1,
        name: 'Checkin Action',
        action_data: {
          data: {
            checkin_facebook_place_name: 'Facebook Place Name',
            checkin_facebook_place_id: null,
            checkin_min_friend_count: 1,
            checkin_welcome_message: 'Welcome to checkin page',
            checkin_challenge_message: 'Please checkin to complete this action',
            checkin_thankyou_message: 'Thank you for checkin'
          },
          action_id: 203
        }
      };

      var checkinAddView = new CheckinAddView({
        model: this.model,
        vent: this.options.vent,
        action: checkinDefaultAction,
        triggerModal: 'showAddModal',
        add: true
      });
      
      $('ul.criteria-list', this.el).append(checkinAddView.render().el);
      
      return checkinAddView;
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
      reward.status = $('select.reward-status', this.el).val() || reward.status;
      reward.description = $('textarea.reward-description', this.el).text() || reward.description;

      this.model.set('reward', reward).trigger('change');
            
      this.options.vent.trigger('showAddModal', this.model);
    },

    cancelEditReward: function(e){
      e.preventDefault();
      $('div.edit-reward', this.el).slideUp();
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
      if(!this.model.get('end_date') || !this.model.get('start_date')){
        alert('Please set start and end date');
        return;
      }
      
      console.log('create challenge!');
      this.model.set('company_id', window.Company.companyId);
      this.model.set('active', true);
      this.options.challengesCollection.create(this.model, {
        success: function() {
          //Refresh
          window.location = window.Company.BASE_URL + 'r/company/' + window.Company.companyId;
        }
      });
      
      this.$el.modal('hide');
    },

    changeReward: function() {
      var challengeRewards = this.challengeRewards;
      var challengeId = $('select.select-challenge-reward', this.el).find('option:selected').data('challengeId');
      if(!challengeId) { return ;}
      var chosenChallenge = challengeRewards[challengeId];
      
      this.model.set('reward', {_id: chosenChallenge._id});

      $('input.reward-name', this.el).val(chosenChallenge.name);
      $('input.reward-image', this.el).val(chosenChallenge.image);
      $('input.reward-value', this.el).val(chosenChallenge.value);
      $('select.reward-status', this.el).val(chosenChallenge.status);
      $('textarea.reward-description', this.el).text(chosenChallenge.description);

      this.saveEditReward();
    },
    
    toggleRepeat: function(){
      var enable = !_.isUndefined($('input.repeat-enable', this.el).attr('checked'));
      var value = parseInt($('input.repeat-interval', this.el).val(), 10);
      if(enable){
        $('div.edit-repeat').show();
        $('div.view-repeat').hide();
        this.model.set('repeat', value).trigger('change');
      }else{
        $('div.view-repeat').hide();
        $('div.edit-repeat').hide();
        this.model.set('repeat', null).trigger('change');
      }
    },
    
    saveRepeat: function(){
      var value = parseInt($('input.repeat-interval', this.el).val(), 10);
      
      if(!value || value < 0){
        alert('number of days should be a number');
        return;
      }
      
      $('div.view-repeat').show();
      $('div.edit-repeat').hide();
      
      this.model.set('repeat', value).trigger('change');
      
      console.log('save repeat', value, this.model.toJSON());
      
      this.options.vent.trigger('showAddModal', this.model);
    },
    
    showEditRepeat: function(){
      $('div.edit-repeat').show();
      $('div.view-repeat').hide();
    },

    showRecipeModal: function() {
      var recipeModal = $('#recipe-modal');
      recipeModal.html(this.recipeTemplate()).modal('show');

      var recipe = null, reward = null;
      //On recipe click
      $('.recipes button', recipeModal).click(function() {
        $('.recipes button', recipeModal).addClass('disabled');
        $(this).removeClass('disabled');
        recipe = $(this).data('recipe');
      });

      //On reward click
      $('.rewards button', recipeModal).click(function() {
        $('.rewards button', recipeModal).addClass('disabled');
        $(this).removeClass('disabled');
        reward = $(this).data('reward');
      });

      var self = this;
      //Choose recipe
      $('button.choose-recipe', recipeModal).click(function(e) {
        if(recipe === 'share') {
          // self.addShare(e);
        } else if(recipe === 'feedback') {
          self.addFeedback(e);
        } else if(recipe === 'checkin') {
          self.addCheckin(e);
        } else if(recipe === 'qr') {
          self.addQR(e);
        }

        if(reward === 'points') {
          //@TODO
        } else if(reward === 'discount') {
          //@TODO
        } else if(reward === 'giveaway') {
          //@TODO
        }
      });
    }
    
  });
  return EditModalView;
});
