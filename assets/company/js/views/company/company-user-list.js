define([
  'jquery',
  'underscore',
  'backbone',
  'models/company-user',
  'text!templates/company/user-list.html',
  'views/company/user-item'
], function($, _, Backbone, CompanyUserModel, userListTemplate, CompanyUserItemView){
  var CompanyUserListPane = Backbone.View.extend({

    events: {
      'click button.load-more' : 'loadMore',
      'click a.user-filter-all': 'loadAll'
    },

    initialize: function(){
      _.bindAll(this);
      this.collection.bind('reset', this.addAll);
      this.collection.bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(_.template(userListTemplate)());

      this.addAll();

      if(this.collection.model.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      }

      return this;
    },

    addOne: function(model){
      // console.log('add one user:', model.toJSON());

      var user = new CompanyUserItemView({
        model: model,
        vent: this.options.vent
      });
      // console.log($('.user-list', this.el));
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
