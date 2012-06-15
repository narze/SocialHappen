define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/sidebar.html'
], function($, _, Backbone, ChallengeModel, sidebarTemplate){
  var SideBarView = Backbone.View.extend({
    sidebarTemplate: _.template(sidebarTemplate),
    
    events: {
      'click button.add-challenge': 'showAddChallenge'
    },

    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      var data = this.options.company;
      if(data){
        $(this.el).html(this.sidebarTemplate(data));
      }
      
      return this;
    },
    
    showAddChallenge: function(){
      console.log('show add challenge');
      var newModel = new ChallengeModel({});
      newModel.set({
        detail: {
          name: 'Challenge Name',
          description: 'Challenge Description',
          image: 'https://lh3.googleusercontent.com/XBLfCOS_oKO-XjeYiaOAuIdukQo9wXMWsdxJZLJO8hvWMBLFwCU3r_0BrRMn_c0TnEDarKuxDg=s640-h400-e365'
        },
        hash: null,
        criteria: [],
        active: false
      });
      console.log('new model:', newModel.toJSON(), 'default:', newModel.defaults);
      this.options.vent.trigger('showAddModal', newModel);
    }
    
  });
  return SideBarView;
});
