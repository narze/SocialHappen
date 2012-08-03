define([
  'jquery',
  'underscore',
  'backbone',
  'models/activity',
  'text!templates/company/activity-list.html',
  'views/company/activity-item'
], function($, _, Backbone, ActivityModel, activityListTemplate, ActivityItemView){
  var ActivityListPane = Backbone.View.extend({

    events: {
      'click button.load-more' : 'loadMore',
      'click a.activity-filter-all': 'loadAll'
    },

    initialize: function(){
      _.bindAll(this);
      this.collection.unbind('reset').bind('reset', this.addAll);
      this.collection.unbind('add').bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(_.template(activityListTemplate)());

      this.addAll();

      if(this.collection.model.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      }

      return this;
    },

    addOne: function(model){
      // console.log('add one activity:', model.toJSON());

      var activity = new ActivityItemView({
        model: model,
        vent: this.options.vent
      });
      // console.log($('.activity-list', this.el));
      var el = activity.render().$el;
      $('.activity-list', this.el).append(el);
    },

    addAll: function(){
      $('.activity-list', this.el).html('');

      if(this.collection.models.length === 0){
        $('.activity-list', this.el).html('Your company have no activity.');
      }

      this.collection.each(function(model){
        this.addOne(model);
      }, this);
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      this.collection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled');
        }else{
          button.addClass('hide');
        }

      });
    },

    loadAll: function() {
      this.collection.loadAll();
    }
  });
  return ActivityListPane;
});
