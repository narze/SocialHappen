define([
  'jquery',
  'underscore',
  'backbone',
  'models/company-user',
  'text!templates/company/user-list.html',
  'views/company/user-item',
  'events'
], function($, _, Backbone, CompanyUserModel, userListTemplate, CompanyUserItemView, vent){
  var CompanyUserListPane = Backbone.View.extend({

    events: {
      'click button.load-more' : 'loadMore',
      'click a.user-filter-all': 'loadAll'
    },

    initialize: function(){
      _.bindAll(this);
      this.collection.unbind('reset').bind('reset', this.addAll);
      this.collection.unbind('add').bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(_.template(userListTemplate)());

      if(this.collection.model.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      }

      return this;
    },

    addOne: function(model){
      // console.log('add one user:', model.toJSON());

      var user = new CompanyUserItemView({
        model: model,
        vent: vent
      });
      var el = user.render().$el;
      $('.user-list', this.el).append(el);
    },

    addAll: function(){
      $('.user-list', this.el).html('');

      if(this.collection.models.length === 0){
        $('.user-list', this.el).html('Your company have no user.');
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
  return CompanyUserListPane;
});
