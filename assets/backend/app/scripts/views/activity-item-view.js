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
        var activity;
        activity = this.model.toJSON();
        switch (activity.audit_description) {
          case "Add Credits":
            activity.audit_description_append = activity.object + " credits";
            break;
          case "Credit Use From Challenge":
            activity.audit_description_append = activity.subject + " credits";
            break;
          default:
            activity.audit_description_append = false;
        }
        this.$el.html(_.template(ActivityItemTemplate, activity));
        this.delegateEvents();
        this.$('.audit-tooltip').tooltip();
        return this;
      }
    });
    return View;
  });

}).call(this);
