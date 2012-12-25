(function() {

  define(['backbone', 'text!templates/activity-item-template.html'], function(Backbone, ActivityItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'activity-item',
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', 'render');
      },
      render: function() {
        this.$el.html(_.template(ActivityItemTemplate, this.model.toJSON()));
        return this;
      }
    });
    return View;
  });

}).call(this);
