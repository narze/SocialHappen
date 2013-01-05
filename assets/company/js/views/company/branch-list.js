define([
  'jquery',
  'underscore',
  'backbone',
  'models/branch',
  'text!templates/company/branch-list.html',
  'views/company/branch-item',
  'masonry',
  'endlessscroll',
  'events',
  'sandbox'
], function($, _, Backbone, BranchModel, branchListTemplate, branchItemView, masonry, endlessscroll, vent, sandbox){
  var BranchListPane = Backbone.View.extend({
    branchListTemplate: _.template(branchListTemplate),

    events: {
      'click button.add-branch': 'showAddbranch',
      'click button.load-more' : 'loadMore'
    },

    initialize: function(){
      _.bindAll(this);
      vent.bind('reloadMasonry', this.reloadMasonry);
      sandbox.collections.branchCollection.bind('reset', this.addAll);
      sandbox.collections.branchCollection.bind('add', this.addOne);
    },

    render: function () {
      $(this.el).html(this.branchListTemplate({}));
      sandbox.collections.branchCollection.fetch();
      console.log('render branch list');
      return this;
    },

    addOne: function(model){
      var branch = new branchItemView({
        model: model,
        vent: vent
      });

      var el = branch.render().$el;
      $('.tile-list', this.el).append(el);
    },

    addAll: function(){
      console.log('addAll');

      $('.tile-list', this.el).masonry({
        // options
        itemSelector : '.item',
        animationOptions: {
          duration: 400
        },
        isFitWidth: true
      });

      $('.tile-list', this.el).html('');

      if(sandbox.collections.branchCollection.length === 0){
        $('.tile-list', this.el).html('Your company have no branch. Start creating a branch by clicking "Create branch" button.');
      }

      if(sandbox.collections.branchCollection.length <= 30){
        $('button.load-more', this.el).addClass('hide');
      } else {
        $('button.load-more', this.el).removeClass('hide');
      }

      sandbox.collections.branchCollection.each(function(model){
        this.addOne(model);
      }, this);
    },

    reloadMasonry: function(){
      $('.tile-list', this.el).masonry('reload');
    },

    loadMore: function(){

      var button = $('button.load-more', this.el).addClass('disabled');
      sandbox.collections.branchCollection.loadMore(function(loaded){
        if(loaded > 0){
          button.removeClass('disabled hide');
        }else{
          button.addClass('hide');
        }

      });
    },

    showAddbranch: function(){
      console.log('show add branch');
      var newModel = new BranchModel({});
      newModel.set({
        title: 'Branch Name',
        photo: 'https://lh5.googleusercontent.com/mww1eX8x-JdWhYUA1B-ovYX3MQf5gGwsqcXvySmebElaBcnKeH0wojdCDSF4rfhnAMlXvsG_=s640-h400-e365',
        address: 'address',
        telephone: '',
        location: [50, 50],
        company_id: window.Company.companyId
      });
      console.log('new model:', newModel.toJSON(), 'default:', newModel.defaults);
      vent.trigger('showAddBranchModal', newModel);
    },

    clean: function() {
      this.remove();
      this.unbind();
      vent.unbind('reloadMasonry');
      sandbox.collections.branchCollection.unbind();
    }
  });
  return BranchListPane;
});
