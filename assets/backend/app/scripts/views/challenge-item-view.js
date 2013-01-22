(function() {

  define(['backbone', 'moment', 'text!templates/challenge-item-template.html'], function(Backbone, moment, ChallengeItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'challenge-item',
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
        return this;
      }
    });
    return View;
  });

}).call(this);
