(function() {

  define(['backbone', 'text!templates/main-template.html', 'views/navbar-view', 'views/sidebar-view', 'views/content-view'], function(Backbone, MainTemplate, NavBarView, SidebarView, ContentView) {
    var View;
    View = Backbone.View.extend({
      initialize: function() {
        return this.render();
      },
      el: $('#app'),
      render: function() {
        this.$el.html(MainTemplate);
        return this.rendered = true;
      }
    });
    window.backend.Views.NavBarView = new NavBarView;
    window.backend.Views.SidebarView = new SidebarView;
    window.backend.Views.ContentView = new ContentView;
    return View;
  });

}).call(this);
