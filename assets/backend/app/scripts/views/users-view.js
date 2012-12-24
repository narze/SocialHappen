(function() {

  define(['backbone'], function(Backbone) {
    var View;
    View = Backbone.View.extend({
      initialize: function() {},
      render: function() {
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
