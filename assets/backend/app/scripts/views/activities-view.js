(function() {

  define(['backbone', 'text!templates/activities-template.html'], function(Backbone, ActivitiesTemplate) {
    var View;
    View = Backbone.View.extend({
      id: 'activities-view',
      initialize: function() {},
      render: function() {
        this.$el.html(ActivitiesTemplate);
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
