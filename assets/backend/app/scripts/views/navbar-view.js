(function() {

  define(['backbone', 'text!templates/navbar-template.html'], function(Backbone, NavbarTemplate) {
    var View;
    return View = Backbone.View.extend({
      initialize: function() {},
      render: function() {
        this.$el.html(NavbarTemplate);
        return this;
      }
    });
  });

}).call(this);
