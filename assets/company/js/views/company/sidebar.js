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
          image: 'https://lh6.googleusercontent.com/JomHaEUw0LXXx3C7iggcx5R42Uu7KB7F9lHXrQqWW16ZGcTjpTs4P2RzKUvwiTAuBXYf4sEHiU8=s640-h400-e365'
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
