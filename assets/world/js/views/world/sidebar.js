define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/world/sidebar.html',
  'views/world/company-item'
], function($, _, Backbone, sidebarTemplate, CompanyItemView){
  var SideBarView = Backbone.View.extend({
    sidebarTemplate: _.template(sidebarTemplate),
    
    initialize: function(){
      _.bindAll(this);
      this.collection.bind('reset', this.addAllCompany);
      this.collection.bind('add', this.addOneCompany);
    },
    
    render: function () {
      this.addAllCompany();
      return this;
    },

    addOneCompany: function(model){
      // console.log('add one challenge:', model.toJSON());
      
      var company = new CompanyItemView({
        model: model,
        vent: this.options.vent
      });

      // var el = company.render().$el;
      $('.companies', this.el).append(company.render().el);
    },
    
    addAllCompany: function(){
      //Reset
      $(this.el).html(this.sidebarTemplate());

      if(!this.collection.length) {
        $('.btn.load-more').html('<div class="no-challenge">No Challenge</div>');
        return false;
      }

      this.collection.each(function(model){
        this.addOneCompany(model);
      }, this);
    }

  });
  return SideBarView;
});
