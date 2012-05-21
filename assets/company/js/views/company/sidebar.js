define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/sidebar.html'
], function($, _, Backbone, sidebarTemplate){
  var SideBarView = Backbone.View.extend({
    sidebarTemplate: _.template(sidebarTemplate),
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      var data = this.options.company;
      $(this.el).html(this.sidebarTemplate(data));
      
      return this;
    }
  });
  return SideBarView;
});
