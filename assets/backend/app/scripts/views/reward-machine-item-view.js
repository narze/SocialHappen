(function() {

  define(['backbone', 'moment', 'text!templates/reward-machine-item-template.html'], function(Backbone, moment, RewardMachineItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'reward-machine-item',
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      "void": function(e) {
        return e.preventDefault();
      },
      render: function() {
        this.$el.html(_.template(RewardMachineItemTemplate, this.model.toJSON()));
        this.delegateEvents();
        return this;
      }
    });
    return View;
  });

}).call(this);
