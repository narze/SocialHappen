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
  'sandbox',
  'vm'
], function($, _, Backbone, pageTemplate, SidebarView, CarouselView, ChallengeListView, RewardListView, CouponListView, ActivityListView, CompanyUserListView, bootstrap, sandbox, Vm){
  var ProfilePage = Backbone.View.extend({
    pageTemplate: _.template(pageTemplate),
    el: '#content',

    events: {
      'click #sidebar .nav-list>li>a': 'setMenuActive'
    },

    initialize: function(){
      _.bindAll(this);
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
        company: company
      });
      sidebarView.render();

      var carouselView = new CarouselView({
        el: $('#carousel', this.el)
      });
      carouselView.render();
    },

    setMenuActive: function(e) {
      $(e.currentTarget).parent('li').siblings().removeClass('active');
      $(e.currentTarget).parent('li').addClass('active');
    }
  });
  return ProfilePage;
});
