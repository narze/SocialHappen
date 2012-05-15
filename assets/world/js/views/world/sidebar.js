define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/world/sidebar.html'
], function($, _, Backbone, sidebarTemplate){
  var SideBarView = Backbone.View.extend({
    sidebarTemplate: _.template(sidebarTemplate),
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.sidebarTemplate({
        
      }));
      
      return this;
    }
  });
  return SideBarView;
});
