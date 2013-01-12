(function() {

  define(['backbone', 'moment', 'text!templates/activity-item-template.html'], function(Backbone, moment, ActivityItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'activity-item',
      events: {
        'click .audit-tooltip': 'void'
      },
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      "void": function(e) {
        return e.preventDefault();
      },
      render: function() {
        this.$el.html(_.template(ActivityItemTemplate, this.model.toJSON()));
        this.delegateEvents();
        this.$('.audit-tooltip').tooltip();
        return this;
      }
    });
    return View;
  });

}).call(this);
