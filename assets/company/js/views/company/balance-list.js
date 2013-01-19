define([
  'jquery',
  'underscore',
  'backbone',
  'models/balance',
  'text!templates/company/balance-list.html',
  'views/company/balance-item',
  'events',
  'sandbox'
], function($, _, Backbone, BalanceModel, balanceListTemplate, BalanceItemView, vent, sandbox){
  var BalanceListPane = Backbone.View.extend({

    events: {
      'click button.load-more' : 'loadMore',
      'click a.balance-filter-all': 'loadAll'
    },

    initialize: function(){
      _.bindAll(this);
      sandbox.collections.balanceCollection.bind('reset', this.addAll);
      sandbox.collections.balanceCollection.bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(_.template(balanceListTemplate)({}));
      sandbox.collections.balanceCollection.fetch();
      return this;
    },

    addOne: function(model){
      console.log('add one balance:', model.toJSON());

      var balance = new BalanceItemView({
        model: model,
        vent: vent
      });
      // console.log($('.balance-list', this.el));
      var el = balance.render().$el;
      $('.balance-list', this.el).append(el);
    },

    addAll: function(){
      console.log('addAll');

      $('.balance-list', this.el).html('');

      if(sandbox.collections.balanceCollection.length === 0){
        $('.balance-list', this.el).html('Your company have no balance.');
      }

      if(sandbox.collections.balanceCollection.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      } else {
        $('button.load-more', this.el).removeClass('hide');
      }

      sandbox.collections.balanceCollection.each(function(model){
        this.addOne(model);
      }, this);
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      sandbox.collections.balanceCollection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled hide');
        }else{
          button.addClass('hide');
        }

      });
    },

    loadAll: function() {
      sandbox.collections.balanceCollection.loadAll();
    },

    clean: function() {
      this.remove();
      this.unbind();
      sandbox.collections.balanceCollection.unbind();
    }
  });
  return BalanceListPane;
});
