(function() {

  define(['backbone'], function(Backbone) {
    var View;
    return View = Backbone.View.extend({
      el: $('#app > #sidebar-view'),
      initialize: function() {
        return this.render();
      },
      render: function() {
        return this.$el.html('sidebarview');
      }
    });
  });

}).call(this);
