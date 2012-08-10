define([
  'jquery',
  'underscore',
  'backbone',
  'models/company-user',
  'text!templates/company/user-list.html',
  'views/company/user-item',
  'events',
  'sandbox'
], function($, _, Backbone, CompanyUserModel, userListTemplate, CompanyUserItemView, vent, sandbox){
  var CompanyUserListPane = Backbone.View.extend({

    events: {
      'click button.load-more' : 'loadMore',
      'click a.user-filter-all': 'loadAll'
    },

    initialize: function(){
      _.bindAll(this);
      sandbox.collections.companyUsersCollection.bind('reset', this.addAll);
      sandbox.collections.companyUsersCollection.bind('add', this.addOne);
      sandbox.collections.companyUsersCollection.fetch();
    },

    render: function () {
      $(this.el).html(_.template(userListTemplate)());

      if(sandbox.collections.companyUsersCollection.model.length <= 30){
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
      console.log('addAll');
      $('.user-list', this.el).html('');

      if(sandbox.collections.companyUsersCollection.models.length === 0){
        $('.user-list', this.el).html('Your company have no user.');
      }

      sandbox.collections.companyUsersCollection.each(function(model){
        this.addOne(model);
      }, this);
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      sandbox.collections.companyUsersCollection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled');
        }else{
          button.addClass('hide');
        }

      });
    },

    loadAll: function() {
      sandbox.collections.companyUsersCollection.loadAll();
    },

    clean: function() {
      this.remove();
      this.unbind();
      sandbox.collections.companyUsersCollection.unbind();
    }
  });
  return CompanyUserListPane;
});
