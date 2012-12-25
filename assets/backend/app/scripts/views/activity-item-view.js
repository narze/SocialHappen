(function() {

  define(['backbone', 'moment', 'text!templates/activity-item-template.html'], function(Backbone, moment, ActivityItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'activity-item',
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      render: function() {
        this.$el.html(_.template(ActivityItemTemplate, this.model.toJSON()));
        return this;
      }
    });
    return View;
  });

}).call(this);
