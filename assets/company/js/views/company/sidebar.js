define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/sidebar.html',
  'sandbox'
], function($, _, Backbone, sidebarTemplate, sandbox){
  var SideBarView = Backbone.View.extend({
    sidebarTemplate: _.template(sidebarTemplate),

    events: {
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      var data = this.options.company;
      if(data){
        data.now = sandbox.now;
        data.companyId = window.Company.companyId;
        $(this.el).html(this.sidebarTemplate(data));
      }

      return this;
    }

  });
  return SideBarView;
});
