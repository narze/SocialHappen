(function() {

  define(['backbone', 'text!templates/sidebar-template.html'], function(Backbone, SidebarTemplate) {
    var View;
    return View = Backbone.View.extend({
      initialize: function() {},
      render: function() {
        this.$el.html(SidebarTemplate);
        return this;
      }
    });
  });

}).call(this);
