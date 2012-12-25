(function() {

  define(['backbone', 'text!templates/user-item-template.html'], function(Backbone, UserItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'user-item',
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', 'render');
      },
      render: function() {
        this.$el.html(_.template(UserItemTemplate, this.model.toJSON()));
        return this;
      }
    });
    return View;
  });

}).call(this);
