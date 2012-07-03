define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/edit.html',
  'text!templates/company/modal/activity-item.html',
  'text!templates/company/modal/challengers-item-template.html',
  'views/company/modal/action/feedback-edit',
  'views/company/modal/action/feedback-add',
  'views/company/modal/action/qr-edit',
  'views/company/modal/action/qr-add',
  'views/company/modal/action/checkin-edit',
  'views/company/modal/action/checkin-add',
  'jqueryui'
], function($, _, Backbone, editTemplate, activityItemTemplate, challengersItemTemplate, FeedbackEditView, FeedbackAddView,
  QREditView, QRAddView, CheckinEditView, CheckinAddView, jqueryui){
  var EditModalView = Backbone.View.extend({
    editTemplate: _.template(editTemplate),
    activityItemTemplate: _.template(activityItemTemplate),
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
      'click button.deactive-challenge': 'deactiveChallenge',
      'click button.edit-reward': 'showEditReward',
      'click button.save-reward': 'saveEditReward',
      'click button.cancel-edit-reward': 'cancelEditReward',
      'click button.edit-score': 'showEditScore',
      'click button.save-score': 'saveEditScore',
      'click button.show-activity': 'showActivity',
      'click button.hide-activity': 'hideActivity',
      'change input.repeat-enable': 'toggleRepeat',
      'click button.save-repeat-interval': 'saveRepeat',
      'click div.view-repeat': 'showEditRepeat'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showEditModal', this.show);
    },

    render: function () {
      console.log('render modal');
      
      if(!this.model){
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
              endDate *= 1000;
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
      
      $('select.select-challenge-reward', this.el).change(this.changeReward);

      //Show challengers
      $.ajax({
        dataType: 'json',
        type: 'POST',
        url: window.Company.BASE_URL + 'apiv3/get_challengers/' + this.model.id + '/' + 1,
        success: function(resp) {
          if(resp.in_progress.length) {
            _.each(resp.in_progress, function(user) {
              $('.joined', self.el).append('<div class="joined-user"><img src="'+ user.user_image +'" alt="'+user.user_first_name+'" title="'+user.user_first_name+'"/></div>');
            });
          } else {
            $('.joined', self.el).append('None');
          }

          if(resp.completed.length) {
            _.each(resp.completed, function(user) {
              $('.completed', self.el).append('<div class="completed-user"><img src="'+ user.user_image +'" alt="'+user.user_first_name+'" title="'+user.user_first_name+'"/></div>');
            });
          } else {
            $('.completed', self.el).append('None');
          }

          $('.load-more-in-progress', self.el).click(loadMoreInProgress);
          $('.load-more-completed', self.el).click(loadMoreCompleted);
        }
      });

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
        var limit = 5;
        var offset = self.challengeInProgressIndex;
        var inProgressTemplate = _.template(challengersItemTemplate);
        var challengeHash = self.model.id;
        $('.load-more-in-progress', self.el).hide();
        $.ajax({
          type: 'POST',
          dataType: 'json',
          url: window.Company.BASE_URL + 'apiv3/get_challengers/' + challengeHash + '/' + limit + '/' + offset,
          success: function (resp) {
            if(!resp.in_progress || (resp.in_progress.length < limit)) {
              return;
            }
            $('.load-more-in-progress', self.el).show();
            _.each(resp.in_progress, function(user) {
              $('.challengers-in-progress', self.el).append(inProgressTemplate(user));
            });
          }
        });
        self.challengeInProgressIndex = offset + limit;
      }
      
      function loadMoreCompleted() {
        var limit = 5;
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
            _.each(resp.completed, function(user) {
              $('.challengers-completed', self.el).append(completedTemplate(user));
            });
          }
        });
        self.challengeCompletedIndex = offset + limit;
      }
      
      return this;
    },
    challengeInProgressIndex: 1,
    challengeCompletedIndex: 1,
    
    show: function(model){
      //skip getting reward_item if already fetched
      console.log('show');
      if(model.get('reward')._id) {
        return this.showEdit(model);
      }

      var self = this;
      self.model = model;

      //get challenge's reward item
      $.ajax({
        dataType: 'json',
        type: 'POST',
        url: window.Company.BASE_URL + 'apiv3/reward_item/' + self.model.get('reward_item_id'),
        success: function(result) {
          if(result.data) {
            self.model.set('reward', result.data);
          }
          self.showEdit(self.model);
        }
      });
    },

    showEdit: function(model) {
      this.model = model;
      console.log('show edit modal:', model.toJSON());
      this.render();
      
      var criteria = this.model.get('criteria');

      _.each(criteria, function(action){
        var type = action.query.action_id;
        if(type == 202){
          var feedbackEditView = new FeedbackEditView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showEditModal',
            save: true
          });
          
          $('ul.criteria-list', this.el).append(feedbackEditView.render().el);
        }else if(type == 201){
          var qrEditView = new QREditView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showEditModal',
            save: true
          });
          
          $('ul.criteria-list', this.el).append(qrEditView.render().el);
        }else if(type == 203){
          var checkinEditView = new CheckinEditView({
            model: this.model,
            action: action,
            vent: this.options.vent,
            triggerModal: 'showEditModal',
            save: true
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
        triggerModal: 'showEditModal',
        save: true
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
        triggerModal: 'showEditModal',
        save: true
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
        triggerModal: 'showEditModal',
        save: true
      });
      
      $('ul.criteria-list', this.el).prepend(checkinAddView.render().el);
      
      checkinAddView.showEdit();
    },

    showEditReward: function() {
      $('h3.edit-reward', this.el).hide();
      $('div.edit-reward', this.el).show();
    },

    saveEditReward: function(e) {
      var reward = this.model.get('reward');
      reward.name = $('input.reward-name', this.el).val() || reward.name;
      reward.image = $('input.reward-image', this.el).val() || reward.image;
      reward.value = $('input.reward-value', this.el).val() || reward.value;
      reward.status = $('select.reward-status', this.el).val() || reward.status;
      reward.description = $('textarea.reward-description', this.el).text() || reward.description;

      this.model.set('reward', reward).trigger('change');
      this.model.save();

      $('h3.edit-reward', this.el).show();
      $('div.edit-reward', this.el).hide();

      this.options.vent.trigger('showEditModal', this.model);
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
      this.model.save();
      
      $('h3.edit-score', this.el).show();
      $('div.edit-score', this.el).hide();

      this.options.vent.trigger('showEditModal', this.model);
    },

    cancelEditReward: function(e){
      e.preventDefault();
      $('div.edit-reward', this.el).slideUp();
      this.render();
    },

    changeReward: function() {
      var challengeRewards = this.challengeRewards;
      var rewardId = $('select.select-challenge-reward', this.el).find('option:selected').data('rewardId');
      if(!rewardId) { return ;}
      var chosenReward = challengeRewards[rewardId];
      
      this.model.set('reward', {_id: chosenReward._id});

      $('input.reward-name', this.el).val(chosenReward.name);
      $('input.reward-image', this.el).val(chosenReward.image);
      $('input.reward-value', this.el).val(chosenReward.value);
      $('select.reward-status', this.el).val(chosenReward.status);
      $('textarea.reward-description', this.el).text(chosenReward.description);

      this.saveEditReward();
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
    }
    
  });
  return EditModalView;
});