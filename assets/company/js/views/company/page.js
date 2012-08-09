define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/page.html',
  'views/company/sidebar',
  'views/company/carousel',
  'views/company/challenge-list',
  'views/company/reward-list',
  'views/company/coupon-list',
  'views/company/activity-list',
  'views/company/company-user-list',
  'bootstrap',
  'sandbox'
], function($, _, Backbone, pageTemplate, SidebarView, CarouselView, ChallengeListView, RewardListView, CouponListView, ActivityListView, CompanyUserListView, bootstrap, sandbox){
  var ProfilePage = Backbone.View.extend({
    pageTemplate: _.template(pageTemplate),
    el: '#content',

    initialize: function(){
      _.bindAll(this);
      sandbox.models.currentUserModel.bind('change', this.render);
    },

    render: function () {
      var company;
      $(this.el).html(pageTemplate);

      if(sandbox.models.currentUserModel){
        company = _.find(sandbox.models.currentUserModel.get('companies'), function(i){
          return i.company_id === window.Company.companyId;
        });

        if(!company){
          // window.location = window.Company.BASE_URL + 'passport';
        } else {
          var self = this;

          require(['views/company/modal/edit'], function (EditChallenge) {
            if(!self.editChallenge){
              self.editChallenge = new EditChallenge({
                el: $('div#edit-challenge-modal')
              });
            }
          });

          require(['views/company/modal/add'], function (AddChallenge) {
            if(!self.addChallenge){
              self.addChallenge = new AddChallenge({
                el: $('div#add-challenge-modal')
              });
            }
          });

          require(['views/company/modal/reward/edit-reward'], function (EditReward) {
            if(!self.editReward){
              self.editReward = new EditReward({
                el: $('div#edit-reward-modal')
              });
            }
          });

          require(['views/company/modal/reward/add-reward'], function (AddReward) {
            if(!self.addReward){
              self.addReward = new AddReward({
                el: $('div#add-reward-modal')
              });
            }
          });
        }
      }
      var sidebarView = new SidebarView({
        el: $('#sidebar', this.el),
        company: company,
        now: this.options.now
      });
      sidebarView.render();

      var carouselView = new CarouselView({
        el: $('#carousel', this.el)
      });
      carouselView.render();

      if(this.options.now === 'challenge'){
        var challengeListView = new ChallengeListView({
          collection: sandbox.collections.challengesCollection,
          el: $('#content-pane', this.el)
        });

        challengeListView.render();
      } else if(this.options.now === 'reward'){
        var rewardListView = new RewardListView({
          collection: sandbox.collections.rewardsCollection,
          el: $('#content-pane', this.el)
        });

        rewardListView.render();
      } else if(this.options.now === 'coupon'){
        var couponListView = new CouponListView({
          collection: sandbox.collections.couponsCollection,
          el: $('#content-pane', this.el)
        });

        couponListView.render();
      } else if(this.options.now === 'users'){
        var companyUserListView = new CompanyUserListView({
          collection: sandbox.collections.companyUsersCollection,
          el: $('#content-pane', this.el)
        });

        companyUserListView.render();
      } else if(this.options.now === 'activities'){
        var activityListView = new ActivityListView({
          collection: sandbox.collections.activitiesCollection,
          el: $('#content-pane', this.el)
        });

        activityListView.render();
      }
    }
  });
  return ProfilePage;
});
