define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/world/company-item.html'
], function($, _, Backbone, companyItemTemplate){
  var companyItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    companyItemTemplate: _.template(companyItemTemplate),
    events: {

    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
    },
    render: function () {
      console.log('render company item');
      var data = this.model.toJSON();
      data.baseUrl = window.Company.BASE_URL;
      $(this.el).html(this.companyItemTemplate(data));
      return this;
    }
  });
  return companyItemView;
});
