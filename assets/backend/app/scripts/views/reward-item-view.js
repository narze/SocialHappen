(function() {

  define(['backbone', 'moment', 'text!templates/reward-item-template.html'], function(Backbone, moment, RewardItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'reward-item',
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      "void": function(e) {
        return e.preventDefault();
      },
      render: function() {
        this.$el.html(_.template(RewardItemTemplate, this.model.toJSON()));
        this.delegateEvents();
        return this;
      }
    });
    return View;
  });

}).call(this);
