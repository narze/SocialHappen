(function() {

  define(['backbone', 'moment', 'text!templates/user-item-template.html'], function(Backbone, moment, UserItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'user-item',
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      render: function() {
        this.$el.html(_.template(UserItemTemplate, this.model.toJSON()));
        return this;
      }
    });
    return View;
  });

}).call(this);
