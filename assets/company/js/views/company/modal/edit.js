define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/edit.html',
  'text!templates/company/modal/coupon-item.html',
  'text!templates/company/modal/activity-item.html',
  'text!templates/company/modal/challengers-item-template.html',
  'text!templates/company/modal/addAction.html',
  'text!templates/company/modal/addReward.html',
  'views/company/modal/action/feedback-action',
  'views/company/modal/action/qr-action',
  'views/company/modal/action/checkin-action',
  'views/company/modal/reward/reward',
  'jqueryui',
  'timeago',
  'collections/challenger'
], function($, _, Backbone, editTemplate, couponItemTemplate, activityItemTemplate, challengersItemTemplate,
  addActionTemplate, addRewardTemplate, FeedbackActionView,
  QRActionView, CheckinActionView, RewardView, jqueryui, timeago, ChallengerCollection){
  var EditModalView = Backbone.View.extend({

    editTemplate: _.template(editTemplate),
    addActionTemplate: _.template(addActionTemplate),
    addRewardTemplate: _.template(addRewardTemplate),
    activityItemTemplate: _.template(activityItemTemplate),
    couponItemTemplate: _.template(couponItemTemplate),

    //Limit for challenger items in list
    limit: 1,
    challengeInProgressIndex: 1,
    challengeCompletedIndex: 1,

    events: {
      'click h3.edit-name': 'showEditName',
      'click button.save-name': 'saveEditName',
      'click div.edit-description': 'showEditDescription',
      'click button.save-description': 'saveEditDescription',
      'click img.challenge-image, h6.edit-image': 'showEditImage',
      'click button.save-image': 'saveEditImage',
      'click button.active-challenge': 'activeChallenge',
      'click button.deactive-challenge': 'deactiveChallenge',
      'click button.show-activity': 'showActivity',
      'click button.hide-activity': 'hideActivity',
      'change input.repeat-enable': 'toggleRepeat',
      'click button.save-repeat-interval': 'saveRepeat',
      'click div.view-repeat': 'showEditRepeat',
      'click .add-new-action': 'showAddNewActionModal',
      'click .add-new-reward': 'showAddNewRewardModal',
      'click button.show-coupon': 'showCoupon',
      'click button.hide-coupon': 'hideCoupon',
      'click button.upload-image-submit': 'uploadImage'
    },

    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showEditModal', this.showEdit)
    },

    render: function () {
      console.log('render modal');

      if(!this.model) {
        return;
      }

      var data = this.model.toJSON();
      $(this.el).html(this.editTemplate(data));

      var self = this;

      //Get all rewards
      if(!data.challengeRewards) {
        $.ajax({
          dataType: 'json',
          type: 'POST',
          url: window.Company.BASE_URL + 'apiv3/get_rewards_for_challenge',
          success: function(resp) {
            self.model.set('challengeRewards', resp.data);
            self.showEdit(self.model);
          },
          error: function() {

          }
        });
      }


      $('#edit_challenge_start').datetimepicker({
        onClose : function(dateText, inst) {
          var date = $('#edit_challenge_start').datetimepicker('getDate');

          var endDate = $('#edit_challenge_end').datetimepicker('getDate');

          if(endDate && date && date >= endDate){
            alert('Start date must come before end date');
            var startDate = self.model.get('start_date');
            if(startDate){
              startDate *= 1000;
              $('#edit_challenge_start').datetimepicker('setDate', (new Date(startDate)));
            }else{
              $('#edit_challenge_start').datetimepicker('setDate', null);
            }
            return;
          }

          self.model.save({
            start_date: Math.floor(date.getTime()/1000)
          });

          self.options.vent.trigger('showEditModal', self.model);
        }
      });
      $('#edit_challenge_end').datetimepicker({
        onClose : function(dateText, inst) {
          var date = $('#edit_challenge_end').datetimepicker('getDate');

          var startDate = $('#edit_challenge_start').datetimepicker('getDate');

          if(date && startDate && startDate >= date){
            alert('End date must come after start date');
            var endDate = self.model.get('end_date');
            if(endDate){
              endDate *= 1000
              $('#edit_challenge_end').datetimepicker('setDate', (new Date(endDate)));
            }else{
              $('#edit_challenge_end').datetimepicker('setDate', null);
            }
            return;
          }

          self.model.save({
            end_date: Math.floor(date.getTime()/1000)
          });

          self.options.vent.trigger('showEditModal', self.model);
        }
      });

      var startDate = this.model.get('start_date');
      if(startDate){
        startDate *= 1000;
        $('#edit_challenge_start').datetimepicker('setDate', (new Date(startDate)));
      }

      var endDate = this.model.get('end_date');
      if(endDate){
        endDate *= 1000;
        $('#edit_challenge_end').datetimepicker('setDate', (new Date(endDate)));
      }

      //Get challengers
      var limit = this.limit;
      var challengerCollection = new ChallengerCollection();
      challengerCollection.url = window.Company.BASE_URL + 'apiv3/get_challengers/' + this.model.id + '/' + limit;
      challengerCollection.fetch({
        success: function(collection, resp) {
          if(resp.in_progress.length) {
            $('.joined', self.el).empty();
            _.each(resp.in_progress, function(user) {
              $('.joined', self.el).append('<div class="joined-user"><img src="'+ user.user_image +'" alt="'+user.user_first_name+'" title="'+user.user_first_name+'"/></div>');
            });
            if(resp.in_progress.length < limit) {
              $('.load-more-in-progress', self.el).hide();
            } else {
              $('.load-more-in-progress', self.el).show().click(loadMoreInProgress);
            }
          } else {
            $('.challenger-joined', self.el).hide();
          }

          if(resp.completed.length) {
            $('.completed', self.el).empty();
            _.each(resp.completed, function(user) {
              $('.completed', self.el).append('<div class="completed-user"><img src="'+ user.user_image +'" alt="'+user.user_first_name+'" title="'+user.user_first_name+'"/></div>');
            });
            if(resp.in_progress.length < limit) {
              $('.load-more-completed', self.el).hide();
            } else {
              $('.load-more-completed', self.el).show() .click(loadMoreCompleted);
            }
          } else {
            $('.challenger-completed', self.el).hide();
          }
        }
      })

      //Show challenge status
      var challengeStatus;
      if(this.model.get('active') === false) {
        challengeStatus = 'Draft';
      } else {
        var start = this.model.get('start_date');
        var end = this.model.get('end_date');
        var now = Math.floor(new Date().getTime()/1000);
        if(now < start) {
          challengeStatus = 'Not started';
        } else if(now > end) {
          challengeStatus = 'Ended';
        } else {
          challengeStatus = 'Published';
        }
      }
      $('.challenge-status', this.el).html(challengeStatus);

      function loadMoreInProgress() {
        var offset = self.challengeInProgressIndex;
        var inProgressTemplate = _.template(challengersItemTemplate);
        var challengeHash = self.model.id;

        $('.load-more-in-progress', self.el).hide();
        challengerCollection.url = window.Company.BASE_URL + 'apiv3/get_challengers/' + challengeHash + '/' + limit + '/' + offset;
        challengerCollection.fetch({
          success: function(collection, resp) {
            if(!resp.in_progress || (resp.in_progress.length < limit)) {
              return;
            }
            $('.load-more-in-progress', self.el).show();
            $('.challengers-in-progress', self.el).empty();
            _.each(resp.in_progress, function(user) {
              $('.challengers-in-progress', self.el).append(inProgressTemplate(user));
            });
            self.challengeInProgressIndex = offset + limit;
          }
        })
      }

      function loadMoreCompleted() {
        var offset = self.challengeCompletedIndex;
        var completedTemplate = _.template(challengersItemTemplate);
        var challengeHash = self.model.id;
        $('.load-more-completed', self.el).hide();
        $.ajax({
          type: 'POST',
          dataType: 'json',
          url: window.Company.BASE_URL + 'apiv3/get_challengers/' + challengeHash + '/' + limit + '/' + offset,
          success: function (resp) {
            if(!resp.completed || (resp.completed.length < limit)) {
              return;
            }
            $('.load-more-completed', self.el).show();
            $('.challengers-completed', self.el).empty();
            _.each(resp.completed, function(user) {
              $('.challengers-completed', self.el).append(completedTemplate(user));
            });
          }
        });
        self.challengeCompletedIndex = offset + limit;
      }

      return this;
    },

    showEdit: function(model) {
      this.model = model;
      console.log('show edit modal:', model.toJSON());
      this.render();

      var criteria = this.model.get('criteria');

      _.each(criteria, function(action){
        var type = action.query.action_id;
        if(type == 202){
          var feedbackActionView = new FeedbackActionView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showEditModal',
            save: true
          });

          $('ul.criteria-list', this.el).append(feedbackActionView.render().el);
        }else if(type == 201){
          var qrActionView = new QRActionView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showEditModal',
            save: true
          });

          $('ul.criteria-list', this.el).append(qrActionView.render().el);
        }else if(type == 203){
          var checkinActionView = new CheckinActionView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showEditModal',
            save: true
          });

          $('ul.criteria-list', this.el).append(checkinActionView.render().el);
        }
      }, this);

      var self = this;
      var reward_items = this.model.get('reward_items');
      _.each(reward_items, function(reward_item){
        var rewardView = new RewardView({
          model: self.model,
          vent: self.options.vent,
          triggerModal: 'showEditModal',
          reward_item: reward_item,
          save: true
        });

        $('ul.reward-list', this.el).append(rewardView.render().el);
      });

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
        triggerModal: 'showEditModal',
        add: true,
        save: true
      });

      $('ul.criteria-list', this.el).append(feedbackActionView.render().el);

      return feedbackActionView;
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
        triggerModal: 'showEditModal',
        add: true,
        save: true
      });


      $('ul.criteria-list', this.el).append(qrActionView.render().el);

      return qrActionView;
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
        triggerModal: 'showEditModal',
        add: true,
        save: true
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
        if(reward === 'points') {
          self.addPointsReward(e).showEdit();
        } else if(reward === 'discount') {
          self.addDiscountReward(e).showEdit();
        } else if(reward === 'giveaway') {
          self.addGiveawayReward(e).showEdit();
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
        triggerModal: 'showEditModal',
        reward_item: reward_item,
        save: true,
        add: true
      });

      $('ul.reward-list', this.el).append(rewardView.render().el);

      return rewardView;
    },

    hideActivity: function(){
      console.log('hideActivity');

      $('button.show-activity', this.el).show();
      $('button.hide-activity', this.el).hide();
      $('ul.activity-list', this.el).hide();
    },

    showActivity: function(){
      console.log('showActivity');
      var self = this;
      $.ajax({
        dataType: 'json',
        type: 'POST',
        // contentType: 'application/json',
        data: 'challenge_hashes=' + JSON.stringify([self.model.id]),
        url: window.Company.BASE_URL + 'apiv3/challenge_activity/',
        success: function(result) {

          if(_.isArray(result)){
            if(result.length > 0){
              $('ul.activity-list').html('');
              _.each(result, function(activity){
                activity.timeago = $.timeago(new Date(activity.timestamp*1000));
                $('ul.activity-list').append(self.activityItemTemplate(activity));
              });
            }else{
              $('ul.activity-list').html('no activity');
            }
            $('ul.activity-list', this.el).show();
            $('button.show-activity', this.el).hide();
            $('button.hide-activity', this.el).show();
          }
        }
      });
    },

    toggleRepeat: function(){
      var enable = !_.isUndefined($('input.repeat-enable', this.el).attr('checked'));
      var value = parseInt($('input.repeat-interval', this.el).val(), 10);
      if(enable && value){
        $('div.edit-repeat').show();
        $('div.view-repeat').hide();
        this.model.set('repeat', value).trigger('change');
        this.model.save();
      }else{
        $('div.view-repeat').hide();
        $('div.edit-repeat').hide();
        this.model.set('repeat', null).trigger('change');
        this.model.save();
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
      this.model.save();
      console.log('save repeat', value, this.model.toJSON());

      this.options.vent.trigger('showEditModal', this.model);
    },

    showEditRepeat: function(){
      $('div.edit-repeat').show();
      $('div.view-repeat').hide();
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

    hideCoupon: function(){
      $('button.show-coupon', this.el).show();
      $('button.hide-coupon', this.el).hide();
      $('ul.coupon-list', this.el).hide();
    },

    showCoupon: function(){
      console.log('showCoupon');
      var self = this;
      $.ajax({
        dataType: 'json',
        type: 'GET',
        // contentType: 'application/json',
        data: 'challenge_id=' + self.model.get('_id').$id,
        url: window.Company.BASE_URL + 'apiv3/coupons/',
        success: function(result) {
          console.log(result);
          if(_.isArray(result)){
            if(result.length > 0){
              $('ul.coupon-list').html('');
              _.each(result, function(coupon){
                 $('ul.coupon-list').append(self.couponItemTemplate(coupon));
              });
            }else{
              $('ul.coupon-list').html('No coupons');
            }
            $('ul.coupon-list', this.el).show();
            $('button.show-coupon', this.el).hide();
            $('button.hide-coupon', this.el).show();
          }
        }
      });
    },

    uploadImage: function(e) {
      e.preventDefault();
      var self = this;
      $('form.upload-image', this.el).ajaxSubmit({
        beforeSubmit: function(a,f,o) {
          o.dataType = 'json';
        },
        success: function(resp) {
          if(resp.success) {
            var imageUrl = resp.data;

            // Save image
            var detail = self.model.get('detail');
            detail.image = imageUrl;

            self.model.set('detail', detail).trigger('change');

            //Save change
            self.model.save();

            self.options.vent.trigger('showEditModal', self.model);
            return;
          }
          alert(resp.data);
        }
      })
    }

  });
  return EditModalView;
});
