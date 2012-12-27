(function() {

  define(['backbone', 'moment', 'text!templates/company-item-template.html'], function(Backbone, moment, CompanyItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'company-item',
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      render: function() {
        this.$el.html(_.template(CompanyItemTemplate, this.model.toJSON()));
        return this;
      }
    });
    return View;
  });

}).call(this);
