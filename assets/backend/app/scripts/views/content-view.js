(function() {

  define(['backbone', 'text!templates/content-template.html'], function(Backbone, ContentTemplate) {
    var View;
    return View = Backbone.View.extend({
      id: 'content',
      className: 'span10',
      initialize: function() {},
      render: function() {
        this.$el.html(ContentTemplate);
        return this;
      }
    });
  });

}).call(this);
