define([
  'jquery',
  'underscore',
  'backbone',
  'views/profile/action-item',
  'text!templates/profile/action.html'
], function($, _, Backbone, ActionItemView, actionListTemplate){
  var ProfilePage = Backbone.View.extend({
    actionListTemplate: _.template(actionListTemplate),
    initialize: function(){
      _.bindAll(this);
      this.collection.bind('add', this.addOne);
      this.collection.bind('reset', this.render);
    },
    render: function () {
      this.$el.html(this.actionListTemplate({
        total: this.collection.length,
        header_text: this.options.header_text
      }));

      this.addAll();
      return this;
    },

    addOne: function(model){
      var actionItemView = new ActionItemView({
        model: model
      });

      $('ul.action-list', this.$el).append(actionItemView.render().el);
    },

    addAll: function(){
      var self = this
        , models;

      //Filter
      if(this.options.filter) {

        models = this.collection.filter(function(action) {
          return action.get('action_id') === self.options.filter
        });
      } else {
        models = this.collection.models;
      }
      _.each(models, function(model){
        self.addOne(model);
      });
    }
  });
  return ProfilePage;
});
