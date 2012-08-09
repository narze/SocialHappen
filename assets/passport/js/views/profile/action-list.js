define([
  'jquery',
  'underscore',
  'backbone',
  'views/profile/action-item',
  'text!templates/profile/action.html',
  'sandbox'
], function($, _, Backbone, ActionItemView, actionListTemplate, sandbox){
  var ProfilePage = Backbone.View.extend({
    actionListTemplate: _.template(actionListTemplate),
    el: '.user-right-pane',
    initialize: function(){
      _.bindAll(this);
      sandbox.collections.actionCollection.bind('add', this.addOne);
      sandbox.collections.actionCollection.bind('reset', this.render);
    },
    render: function () {
      this.$el.html(this.actionListTemplate({
        total: sandbox.collections.actionCollection.length,
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

        models = sandbox.collections.actionCollection.filter(function(action) {
          return action.get('action_id') === self.options.filter
        });

      } else {
        models = sandbox.collections.actionCollection.models;
      }

      if(models.length == 0){
        this.$('ul.action-list').html('No action');
      }

      _.each(models, function(model){
        self.addOne(model);
      });
    }
  });
  return ProfilePage;
});
