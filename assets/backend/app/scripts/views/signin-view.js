(function() {

  define(['backbone', 'text!templates/signin-template.html'], function(Backbone, SigninTemplate) {
    var View;
    View = Backbone.View.extend({
      initialize: function() {},
      el: $('#app'),
      render: function() {
        this.$el.html(SigninTemplate);
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
