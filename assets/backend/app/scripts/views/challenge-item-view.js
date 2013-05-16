// Generated by CoffeeScript 1.6.2
define(['backbone', 'moment', 'text!templates/challenge-item-template.html'], function(Backbone, moment, ChallengeItemTemplate) {
  var View;

  View = Backbone.View.extend({
    tagName: 'tr',
    className: 'challenge-item',
    events: {
      'click .view': 'viewItem',
      'click .edit': 'editItem',
      'click .delete': 'deleteItem'
    },
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
    },
    viewItem: function(e) {
      return e.preventDefault();
    },
    editItem: function(e) {
      return e.preventDefault();
    },
    deleteItem: function(e) {
      return e.preventDefault();
    }
  });
  return View;
});
