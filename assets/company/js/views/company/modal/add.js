define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/modal/add.html',
  'text!templates/company/modal/recipe.html',
  'text!templates/company/modal/addAction.html',
  'text!templates/company/modal/addReward.html',
  'views/company/modal/action/feedback-action',
  'views/company/modal/action/qr-action',
  'views/company/modal/action/checkin-action',
  'views/company/modal/reward/reward',
  'jqueryui'
], function($, _, Backbone, ChallengeModel, addTemplate, recipeTemplate,
   addActionTemplate, addRewardTemplate, FeedbackActionView,
   QRActionView, CheckinActionView, RewardView, jqueryui){
  var EditModalView = Backbone.View.extend({

    addTemplate: _.template(addTemplate),
    addActionTemplate: _.template(addActionTemplate),
    addRewardTemplate: _.template(addRewardTemplate),
    recipeTemplate: _.template(recipeTemplate),

    events: {
      'click button.create-challenge': 'createChallenge',
      'change input.repeat-enable': 'toggleRepeat',
      'click button.save-repeat-interval': 'saveRepeat',
      'click div.view-repeat': 'showEditRepeat',
      'click button.save-image': 'saveEditImage',
      'click .add-new-action': 'showAddNewActionModal',
      'click .add-new-reward': 'showAddNewRewardModal',
      'click .cancel': 'cancelAdd',
      'keyup input.challenge-name': 'onTypeChallengeName',
      'keyup textarea.challenge-description': 'onTypeChallengeDescription'
    },

    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showAddModal', this.show);
      this.options.vent.bind('showRecipeModal', this.showRecipeModal);
    },

    render: function () {
      console.log('render modal');
      var data = this.model.toJSON();
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

      // Disable publish button if no action/reward
      if(!this.model.get('reward_items').length || !this.model.get('criteria').length) {
        $('button.create-challenge', this.el).attr('disabled', 'disabled');
      } else {
        $('button.create-challenge', this.el).removeAttr('disabled');
      }

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
          var feedbackActionView = new FeedbackActionView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showAddModal'
          });

          $('ul.criteria-list', this.el).append(feedbackActionView.render().el);
        }else if(type == 201){
          var qrActionView = new QRActionView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showAddModal'
          });

          $('ul.criteria-list', this.el).append(qrActionView.render().el);
        }else if(type == 203){
          var checkinActionView = new CheckinActionView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showAddModal'
          });

          $('ul.criteria-list', this.el).append(checkinActionView.render().el);
        }
      }, this);

      var reward_items = this.model.get('reward_items');
      _.each(reward_items, function(reward_item){
        var rewardView = new RewardView({
          model: this.model,
          vent: this.options.vent,
          triggerModal: 'showAddModal',
          reward_item: reward_item
        });

        $('ul.reward-list', this.el).append(rewardView.render().el);
      }, this);

      this.$el.modal('show');
    },

    onTypeChallengeName: function(e){
       var detail = this.model.get('detail');
      detail.name = $('input.challenge-name', this.el).val();

      this.model.set('detail', detail).trigger('change');
    },

    onTypeChallengeDescription: function(e){
       var detail = this.model.get('detail');
      detail.description = $('textarea.challenge-description', this.el).val();

      this.model.set('detail', detail).trigger('change');
    },

    showEditName: function(){
      $('h3.edit-name', this.el).hide();
      $('div.edit-name', this.el).show();
      $('input.challenge-name', this.el).focus();
    },

    saveEditImage: function(){

      console.log('save image');
      var detail = this.model.get('detail');
      detail.image = $('input.challenge-image', this.el).val();

      this.model.set('detail', detail).trigger('change');

      this.options.vent.trigger('showAddModal', this.model);
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

      var qrActionView = new QRActionView({
        model: this.model,
        vent: this.options.vent,
        action: qrDefaultAction,
        triggerModal: 'showAddModal',
        add: true
      });


      $('ul.criteria-list', this.el).append(qrActionView.render().el);

      return qrActionView;
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
            feedback_welcome_message: 'Welcome',
            feedback_question_message: 'What do you think about us?',
            feedback_vote_message: 'Please give us a rating',
            feedback_thankyou_message: 'Thank you'
          },
          action_id: 202
        }
      };

      var feedbackActionView = new FeedbackActionView({
        model: this.model,
        vent: this.options.vent,
        action: feedbackDefaultAction,
        triggerModal: 'showAddModal',
        add: true
      });

      $('ul.criteria-list', this.el).append(feedbackActionView.render().el);

      return feedbackActionView;
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
            checkin_facebook_place_name: null,
            checkin_facebook_place_id: null,
            checkin_min_friend_count: 1,
            checkin_welcome_message: 'Welcome to checkin page',
            checkin_challenge_message: 'Please checkin to complete this action',
            checkin_thankyou_message: 'Thank you for checkin'
          },
          action_id: 203
        }
      };

      var checkinActionView = new CheckinActionView({
        model: this.model,
        vent: this.options.vent,
        action: checkinDefaultAction,
        triggerModal: 'showAddModal',
        add: true
      });

      $('ul.criteria-list', this.el).append(checkinActionView.render().el);

      return checkinActionView;
    },

    showAddNewRewardModal: function(e) {
      var addActionModal = $('#add-action-modal');
      addActionModal.html(addRewardTemplate).modal('show');
      var reward = null;
      var self = this;
      //On reward click
      $('.rewards button', addActionModal).click(function() {
        $('.rewards button', addActionModal).addClass('disabled');
        $(this).removeClass('disabled');
        reward = $(this).data('reward');
      });

      $('button.choose-recipe', addActionModal).click(function(e) {
        $('.setup-your-reward').hide();
        if(reward === 'points') {
          self.addPointsReward(e).showEdit();
        } else if(reward === 'discount') {
          self.addDiscountReward(e).showEdit();
        } else if(reward === 'giveaway') {
          self.addGiveawayReward(e).showEdit();
        } else {
          $('.setup-your-reward').show();
        }
      });
    },

    addPointsReward: function(e){
      e.preventDefault();

      var reward = {
        name: 'Redeeming Points',
        image: window.Company.BASE_URL + 'assets/images/blank.png',
        value: 10,
        status: 'published',
        type: 'challenge',
        description: '10 Points for redeeming rewards in this company',
        is_points_reward: true
      };

      return this._addReward(reward);
    },

    addDiscountReward: function(e){
      e.preventDefault();

      var reward = {
        name: 'Discount Coupon',
        image: window.Company.BASE_URL + 'assets/images/blank.png',
        value: 0,
        status: 'published',
        type: 'challenge',
        description: '10% discount coupon',
        is_points_reward: false
      };

      return this._addReward(reward);
    },

    addGiveawayReward: function(e){
      e.preventDefault();

      var reward = {
        name: 'Giveaway Reward',
        image: window.Company.BASE_URL + 'assets/images/blank.png',
        value: 0,
        status: 'published',
        type: 'challenge',
        description: 'Giveaway reward',
        is_points_reward: false
      };

      return this._addReward(reward);
    },

    _addReward: function(reward_item) {
      var rewardView = new RewardView({
        model: this.model,
        vent: this.options.vent,
        triggerModal: 'showAddModal',
        reward_item: reward_item,
        add: true
      });

      $('ul.reward-list', this.el).append(rewardView.render().el);

      return rewardView;
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
          window.location = window.Company.BASE_URL + 'r/company_admin/' + window.Company.companyId;
        }
      });

      this.$el.modal('hide');
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

        $('.setup-your-reward').hide();
        if(reward === 'points') {
          self.addPointsReward(e);
        } else if(reward === 'discount') {
          self.addDiscountReward(e);
        } else if(reward === 'giveaway') {
          self.addGiveawayReward(e);
        } else {
          $('.setup-your-reward').show();
        }
      });
    },

    cancelAdd: function(e) {
      console.log('cancel add challenge');
      this.options.action = {};
      this.options.reward_item = {};
      this.model = new ChallengeModel();
      this.model.set('reward_items', null);
    }

  });
  return EditModalView;
});
