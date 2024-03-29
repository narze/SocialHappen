(function() {

  define(['backbone', 'text!templates/main-template.html', 'views/navbar-view', 'views/sidebar-view', 'views/content-view'], function(Backbone, MainTemplate, NavBarView, SidebarView, ContentView) {
    var View;
    View = Backbone.View.extend({
      initialize: function() {},
      el: $('#app'),
      render: function() {
        this.$el.html(MainTemplate);
        window.backend.Views.NavBarView = new NavBarView;
        this.$('#navbar-view').html(window.backend.Views.NavBarView.render().el);
        window.backend.Views.SidebarView = new SidebarView;
        this.$('#sidebar-view').html(window.backend.Views.SidebarView.render().el);
        window.backend.Views.ContentView = new ContentView;
        this.$('#content-view').html(window.backend.Views.ContentView.render().el);
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
