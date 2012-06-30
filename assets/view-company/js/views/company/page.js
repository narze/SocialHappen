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
  'bootstrap'
], function($, _, Backbone, pageTemplate, SidebarView, CarouselView, ChallengeListView, RewardListView, CouponListView, bootstrap){
  var ProfilePage = Backbone.View.extend({
    pageTemplate: _.template(pageTemplate),
    el: '#content',
    
    initialize: function(){
      _.bindAll(this);
      this.options.currentUserModel.bind('change', this.render);
    },
    render: function () {
      var company;
      $(this.el).html(pageTemplate);
      
      if(this.options.currentUserModel){
        company = _.find(this.options.currentUserModel.get('companies'), function(i){
          return i.company_id == window.Company.companyId;
        });
        
        if(!company){
          // window.location = window.Company.BASE_URL + '/play';
        }else{
          var self = this;
      
          require(['views/company/modal/edit'], function (EditChallenge) {
            if(!self.editChallenge){
              self.editChallenge = new EditChallenge({
                currentUserModel: self.options.currentUserModel,
                challengesCollection: self.options.challengesCollection,
                vent: self.options.vent,
                el: $('div#edit-challenge-modal')
              });
            }
          });
          
          require(['views/company/modal/add'], function (AddChallenge) {
            if(!self.addChallenge){
              self.addChallenge = new AddChallenge({
                currentUserModel: self.options.currentUserModel,
                challengesCollection: self.options.challengesCollection,
                vent: self.options.vent,
                el: $('div#add-challenge-modal')
              });
            }
          });
          
          require(['views/company/modal/reward/edit-reward'], function (EditReward) {
            if(!self.editReward){
              self.editReward = new EditReward({
                currentUserModel: self.options.currentUserModel,
                rewardsCollection: self.options.rewardsCollection,
                vent: self.options.vent,
                el: $('div#edit-reward-modal')
              });
            }
          });
          
          require(['views/company/modal/reward/add-reward'], function (AddReward) {
            if(!self.addReward){
              self.addReward = new AddReward({
                currentUserModel: self.options.currentUserModel,
                rewardsCollection: self.options.rewardsCollection,
                vent: self.options.vent,
                el: $('div#add-reward-modal')
              });
            }
          });
        }
      }
      var sidebarView = new SidebarView({
        el: $('#sidebar', this.el),
        currentUserModel: this.options.currentUserModel,
        vent: this.options.vent,
        company: company,
        now: this.options.now
      });
      sidebarView.render();
      
      var carouselView = new CarouselView({
        el: $('#carousel', this.el),
        currentUserModel: this.options.currentUserModel,
        vent: this.options.vent
      });
      carouselView.render();
      
      if(this.options.now == 'challenge'){
        var challengeListView = new ChallengeListView({
          collection: this.options.challengesCollection,
          el: $('#content-pane', this.el),
          vent: this.options.vent
        });
        
        challengeListView.render();
      }else if(this.options.now == 'reward'){
        var rewardListView = new RewardListView({
          collection: this.options.rewardsCollection,
          el: $('#content-pane', this.el),
          vent: this.options.vent
        });
        
        rewardListView.render();
      }else if(this.options.now == 'coupon'){
        var couponListView = new CouponListView({
          collection: this.options.couponsCollection,
          el: $('#content-pane', this.el),
          vent: this.options.vent
        });
        
        couponListView.render();
      }
      
      
    }
  });
  return ProfilePage;
});
