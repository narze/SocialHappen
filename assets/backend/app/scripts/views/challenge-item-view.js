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
        var challengeItem;
        challengeItem = this.model.toJSON();
        challengeItem.branch_sonar_data = challengeItem.branch_sonar_data || [];
        this.$el.html(_.template(ChallengeItemTemplate, challengeItem));
        this.delegateEvents();
        return this;
      }
    });
    return View;
  });

}).call(this);
