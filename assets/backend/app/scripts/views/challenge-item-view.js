(function() {

  define(['backbone', 'moment', 'text!templates/challenge-item-template.html'], function(Backbone, moment, ChallengeItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'challenge-item',
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
        this.$el.html(_.template(ChallengeItemTemplate, this.model.toJSON()));
        this.delegateEvents();
        this.$('.audit-tooltip').tooltip();
        return this;
      }
    });
    return View;
  });

}).call(this);
