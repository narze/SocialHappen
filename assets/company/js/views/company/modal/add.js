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
  'views/company/modal/action/walkin-action',
  'views/company/modal/reward/reward',
  'jqueryui',
  'jqueryForm',
  'events',
  'sandbox',
  'chosen'
], function($, _, Backbone, ChallengeModel, addTemplate, recipeTemplate,
   addActionTemplate, addRewardTemplate, FeedbackActionView,
   QRActionView, CheckinActionView, WalkinActionView, RewardView, jqueryui,
    jqueryForm, vent, sandbox, chosen){
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
      'keyup input.latitude': 'onTypeLatitude',
      'keyup input.longitude': 'onTypeLongitude',
      'keyup input.done-count-max': 'onTypeDoneCountMax',
      'keyup input.sonar-frequency': 'onTypeSonarFrequency',
      'keyup textarea.challenge-description': 'onTypeChallengeDescription',
      'click button.upload-image-submit': 'uploadImage',
      'click button.generate-sonar-data': 'generateSonarData',
      'change input.all-branch-enable': 'toggleAllBranch',
      'change input.verify-location': 'toggleVerifyLocation',
      'change input.custom-location': 'toggleCustomLocation',
      'change select.select-branch': 'onSelectBranch',
      'keyup input.google-maps-link': 'useGoogleMapsLink',
      'click button.view-google-maps': 'viewGoogleMaps'
    },

    initialize: function(){
      _.bindAll(this);
      vent.bind('showAddModal', this.show);
      vent.bind('showRecipeModal', this.showRecipeModal);
    },

    render: function () {
      console.log('render modal');
      var data = this.model.toJSON();
      data.branchList = sandbox.collections.branchCollection.toJSON();
      $(this.el).html(this.addTemplate(data));

      this.$('select.select-branch').val(data.branches);

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
        if(type === 202) {
          var feedbackActionView = new FeedbackActionView({
            model: this.model,
            action: action,
            vent: vent,
            triggerModal: 'showAddModal'
          });

          $('ul.criteria-list', this.el).append(feedbackActionView.render().el);
        } else if(type === 201) {
          var qrActionView = new QRActionView({
            model: this.model,
            action: action,
            vent: vent,
            triggerModal: 'showAddModal'
          });

          $('ul.criteria-list', this.el).append(qrActionView.render().el);
        } else if(type === 203) {
          var checkinActionView = new CheckinActionView({
            model: this.model,
            action: action,
            vent: vent,
            triggerModal: 'showAddModal'
          });

          $('ul.criteria-list', this.el).append(checkinActionView.render().el);
        } else if(type === 204) {
          var walkinActionView = new WalkinActionView({
            model: this.model,
            action: action,
            vent: vent,
            triggerModal: 'showAddModal'
          });

          $('ul.criteria-list', this.el).append(walkinActionView.render().el);
        }
      }, this);

      var reward_items = this.model.get('reward_items');
      _.each(reward_items, function(reward_item){
        var rewardView = new RewardView({
          model: this.model,
          vent: vent,
          triggerModal: 'showAddModal',
          reward_item: reward_item
        });

        $('ul.reward-list', this.el).append(rewardView.render().el);
      }, this);

      this.$(".chzn-select").chosen();

      this.$el.modal('show');
    },

    toggleAllBranch: function(){
      var enable = !_.isUndefined($('input.all-branch-enable', this.el).attr('checked'));

      if(!enable){
        this.$('div.select-branch').removeClass('hide');
      }else{
        this.$('div.select-branch').addClass('hide');
      }

      this.model.set('all_branch', enable).trigger('change');
    },

    onSelectBranch: function(){
      var branches = this.$('select.select-branch').val();
      this.model.set('branches', branches).trigger('change');
      vent.trigger('showAddModal', this.model);
      console.log('select branch', branches);
    },

    toggleVerifyLocation: function(){
      var enable = !_.isUndefined($('input.verify-location', this.el).attr('checked'));

      this.model.set('verify_location', enable).trigger('change');
      vent.trigger('showAddModal', this.model);
    },

    toggleCustomLocation: function(){
      var enable = !_.isUndefined($('input.custom-location', this.el).attr('checked'));

      if(enable){
        this.$('div.custom-location-lat-lng').removeClass('hide');
      }else{
        this.$('div.custom-location-lat-lng').addClass('hide');
      }

      this.model.set('custom_location', enable).trigger('change');
      vent.trigger('showAddModal', this.model);
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

    onTypeLongitude: function(e){
      console.log('location change');
      var location = this.model.get('location');
      var longitude = $('input.longitude', this.el).val();

      location[0] = longitude;

      this.model.set('location', location).trigger('change');
    },

    onTypeLatitude: function(e){
      console.log('location change');
      var location = this.model.get('location');
      var latitude = $('input.latitude', this.el).val();

      location[1] = latitude;

      this.model.set('location', location).trigger('change');
    },

    onTypeDoneCountMax: function(e){
      console.log('done count max change');
      var done_count_max = $('input.done-count-max', this.el).val();

      this.model.set('done_count_max', done_count_max).trigger('change');
    },

    onTypeSonarFrequency: function(e){
      console.log('sonar frequency change');
      var sonar_frequency = $('input.sonar-frequency', this.el).val();

      this.model.set('sonar_frequency', sonar_frequency).trigger('change');
    },

    generateSonarData: function() {
      var self = this
      $.ajax({
        type: 'GET',
        url: window.Company.BASE_URL + 'apiv3/get_sonar_box_data',
        dataType: 'JSON',
        success: function(res) {
          if(res.success) {
            self.model.set('sonar_frequency', res.data).trigger('change');
            $('.sonar-frequency', self.el).val(res.data)
          }
        }
      })
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

      vent.trigger('showAddModal', this.model);
    },

    saveEditName: function(){
      var detail = this.model.get('detail');
      detail.name = $('input.challenge-name', this.el).val();

      this.model.set('detail', detail).trigger('change');

      $('h3.edit-name', this.$el).show();
      $('div.edit-name', this.$el).hide();

      vent.trigger('showAddModal', this.model);
    },

    showAddNewActionModal: function(e) {
      var addActionModal = $('#add-action-modal');
      addActionModal.html(addActionTemplate).modal('show');
      var recipe = 'walkin';
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
        } else if(recipe === 'walkin') {
          self.addWalkin(e).showEdit();
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
        vent: vent,
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
        vent: vent,
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
        vent: vent,
        action: checkinDefaultAction,
        triggerModal: 'showAddModal',
        add: true
      });

      $('ul.criteria-list', this.el).append(checkinActionView.render().el);

      return checkinActionView;
    },

    addWalkin: function(e){
      e.preventDefault();
      console.log('show add walkin');

      var walkinDefaultAction = {
        query: {
          action_id: 204
        },
        count: 1,
        name: 'Walkin',
        action_data: {
          data: {

          },
          action_id: 204
        }
      };

      var walkinActionView = new WalkinActionView({
        model: this.model,
        vent: vent,
        action: walkinDefaultAction,
        triggerModal: 'showAddModal',
        add: true
      });

      $('ul.criteria-list', this.el).append(walkinActionView.render().el);

      return walkinActionView;
    },

    showAddNewRewardModal: function(e) {
      var addActionModal = $('#add-action-modal');
      addActionModal.html(addRewardTemplate).modal('show');
      var reward = 'points';
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
        is_points_reward: true,
        redeem_method: 'in_store'
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
        is_points_reward: false,
        redeem_method: 'in_store'
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
        is_points_reward: false,
        redeem_method: 'in_store'
      };

      return this._addReward(reward);
    },

    _addReward: function(reward_item) {
      var rewardView = new RewardView({
        model: this.model,
        vent: vent,
        triggerModal: 'showAddModal',
        reward_item: reward_item,
        add: true
      });

      $('ul.reward-list', this.el).append(rewardView.render().el);

      return rewardView;
    },

    createChallenge: function(){
      this.$('.edit-name.control-group').removeClass('error');

      if(!this.model.get('end_date') || !this.model.get('start_date')){
        alert('Please set start and end date');
        return;
      }

      var detail = this.model.get('detail');
      if(detail.name.length == 0){
        alert('Please insert challenge name');
        this.$('.edit-name.control-group').addClass('error');
        return;
      }

      console.log('create challenge!');
      this.model.set('company_id', window.Company.companyId);
      this.model.set('active', true);
      var self = this;
      sandbox.collections.challengesCollection.create(this.model, {
        success: function(model, res) {
          if(res.success) {
            self.$el.modal('hide');
            window.location = window.Company.BASE_URL + 'r/company_admin/' + window.Company.companyId;
          } else {
            self.model.trigger('destroy');
            sandbox.collections.challengesCollection.remove(self.model);
            alert('Challenge create failed (session timeout)')
          }
        },
        error: function(){
          self.model.trigger('destroy');
          sandbox.collections.challengesCollection.remove(self.model);
          alert('Challenge create failed')
        }
      });
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

      vent.trigger('showAddModal', this.model);
    },

    showEditRepeat: function(){
      $('div.edit-repeat').show();
      $('div.view-repeat').hide();
    },

    showRecipeModal: function() {
      var recipeModal = $('#recipe-modal');
      recipeModal.html(this.recipeTemplate()).modal('show');

      var recipe = 'walkin', reward = 'points';
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
        } else if(recipe === 'walkin') {
          self.addWalkin(e);
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

        // Disable publish button if no action/reward
        if(!self.model.get('reward_items').length || !self.model.get('criteria').length) {
          $('button.create-challenge', self.el).attr('disabled', 'disabled');
        } else {
          $('button.create-challenge', self.el).removeAttr('disabled');
        }
      });
    },

    cancelAdd: function(e) {
      console.log('cancel add challenge');
      this.options.action = {};
      this.options.reward_item = {};
      this.model = new ChallengeModel();
      this.model.set('reward_items', null);
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

            vent.trigger('showAddModal', self.model);
            return;
          }
          alert(resp.data);
        }
      })
    },

    useGoogleMapsLink: function(e) {
      e.preventDefault();

      var link = this.$('input.google-maps-link').val()
        , latlng = getParameterByName(link, 'q').split(',')
        , lat = latlng[0]
        , lng = latlng[1]

      if(lat && lng) {
        this.$('input.latitude').val(lat)
        this.$('input.longitude').val(lng)
      }

      function getParameterByName(string, name)
      {
        string = "?" + string.split('?')[1];
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regexS = "[\\?&]" + name + "=([^&#]*)";
        var regex = new RegExp(regexS);
        var results = regex.exec(string);
        if(results === null)
          return "";
        else
          return decodeURIComponent(results[1].replace(/\+/g, " "));
      }
    },

    viewGoogleMaps: function(e) {
      e.preventDefault();

      var self = this
        , marker = false
        , $formLatitude = this.$('input.latitude')
        , $formLongitude = this.$('input.longitude')

      require(['gmaps'], function(GMaps) {
        $('#gmaps').css({
          width: '100%',
          height: 300
        });

        var map = new GMaps({
          div: '#gmaps',
          lat: 0,
          lng: 0,
          zoom: 16,
          click: function(e) {
            console.log(e);
            $formLatitude.val(e.latLng.Ya)
            $formLongitude.val(e.latLng.Za)

            if(!!marker) {
              map.removeMarker(marker);
            }
            marker = map.addMarker({
              lat: e.latLng.Ya,
              lng: e.latLng.Za
            });

            map.refresh();
          }
        });

        if(!$formLatitude.val().length && !$formLongitude.val().length) {
          GMaps.geolocate({
            success: function(position) {
              map.setCenter(position.coords.latitude, position.coords.longitude);
            },
            error: function(error) {
              console.log('Geolocation failed: '+error.message);
            },
            not_supported: function() {
              console.log("Your browser does not support geolocation");
            },
            always: function() {
              console.log("Done!");
            }
          });
        } else {
          map.setCenter($formLatitude.val(), $formLongitude.val());
          marker = map.addMarker({
            lat: $formLatitude.val(),
            lng: $formLongitude.val()
          });
        }
      });
    }

  });
  return EditModalView;
});