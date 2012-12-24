(function() {

  define(['backbone', 'text!templates/users-template.html'], function(Backbone, UsersTemplate) {
    var View;
    View = Backbone.View.extend({
      id: 'users-view',
      initialize: function() {},
      render: function() {
        this.$el.html(UsersTemplate);
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
