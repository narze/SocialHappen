(function() {

  define(['backbone', 'views/navbar-view', 'views/sidebar-view', 'views/content-view'], function(Backbone, NavBarView, SidebarView, ContentView) {
    var View;
    View = Backbone.View.extend({});
    window.backend.Views.NavBarView = new NavBarView;
    window.backend.Views.SidebarView = new SidebarView;
    window.backend.Views.ContentView = new ContentView;
    return View;
  });

}).call(this);
