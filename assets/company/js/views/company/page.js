define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/page.html',
  'views/company/sidebar',
  'views/company/carousel',
  'views/company/challenge-list',
  'bootstrap'
], function($, _, Backbone, pageTemplate, SidebarView, CarouselView, ChallengeListView, bootstrap){
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
        }
      }
      var sidebarView = new SidebarView({
        el: $('#sidebar', this.el),
        currentUserModel: this.options.currentUserModel,
        vent: this.options.vent,
        company: company
      });
      sidebarView.render();
      
      var carouselView = new CarouselView({
        el: $('#carousel', this.el),
        currentUserModel: this.options.currentUserModel,
        vent: this.options.vent
      });
      carouselView.render();
      
      var challengeListView = new ChallengeListView({
        collection: this.options.challengesCollection,
        el: $('#challenge-list', this.el),
        vent: this.options.vent
      });
      
      challengeListView.render();
    }
  });
  return ProfilePage;
});
