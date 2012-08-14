define([
  'jquery',
  'underscore',
  'backbone',
  'models/activity',
  'text!templates/company/activity-list.html',
  'views/company/activity-item',
  'events',
  'sandbox'
], function($, _, Backbone, ActivityModel, activityListTemplate, ActivityItemView, vent, sandbox){
  var ActivityListPane = Backbone.View.extend({

    events: {
      'click button.load-more' : 'loadMore',
      'click a.activity-filter-all': 'loadAll'
    },

    initialize: function(){
      _.bindAll(this);
      sandbox.collections.activitiesCollection.bind('reset', this.addAll);
      sandbox.collections.activitiesCollection.bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(_.template(activityListTemplate)({}));
      sandbox.collections.activitiesCollection.fetch();
      return this;
    },

    addOne: function(model){
      // console.log('add one activity:', model.toJSON());

      var activity = new ActivityItemView({
        model: model,
        vent: vent
      });
      // console.log($('.activity-list', this.el));
      var el = activity.render().$el;
      $('.activity-list', this.el).append(el);
    },

    addAll: function(){
      console.log('addAll');

      $('.activity-list', this.el).html('');

      if(sandbox.collections.activitiesCollection.length === 0){
        $('.activity-list', this.el).html('Your company have no activity.');
      }

      if(sandbox.collections.activitiesCollection.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      } else {
        $('button.load-more', this.el).removeClass('hide');
      }

      sandbox.collections.activitiesCollection.each(function(model){
        this.addOne(model);
      }, this);
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      sandbox.collections.activitiesCollection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled hide');
        }else{
          button.addClass('hide');
        }

      });
    },

    loadAll: function() {
      sandbox.collections.activitiesCollection.loadAll();
    },

    clean: function() {
      this.remove();
      this.unbind();
      sandbox.collections.activitiesCollection.unbind();
    }
  });
  return ActivityListPane;
});
