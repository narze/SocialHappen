define([
  'jquery',
  'underscore',
  'backbone',
  'views/company/challenger-item',
  'text!templates/company/challenger-list.html',
  'sandbox'
], function($, _, Backbone, ChallengerItem, ChallengerListTemplate, sandbox){
  var ChallengerList = Backbone.View.extend({

    challengerListTemplate: _.template(ChallengerListTemplate),

    limit: 5,

    challengersObj: null,

    events: {
      'click .load-more-in-progress': 'loadMoreInProgress',
      'click .load-more-completed': 'loadMoreCompleted'
    },

    initialize: function(){
      _.bindAll(this);
      sandbox.models.challengerModel.bind('change', this.prepareAddAll);
    },

    render: function () {
      $(this.el).html(this.challengerListTemplate({}));
      sandbox.models.challengerModel.url = window.Company.BASE_URL + 'apiv3/get_challengers/' + sandbox.challengeHash;
      sandbox.models.challengerModel.fetch();
      return this;
    },

    prepareAddAll: function() {
      console.log('addAll');
      $('ul', this.el).empty();

      if(!this.challengersObj) {
        this.challengersObj = sandbox.models.challengerModel.toJSON();
      }

      if(this.challengersObj.in_progress.length === 0){
        $('ul.in-progress', this.el).html('No user in progress');
      }
      if(this.challengersObj.completed.length === 0){
        $('ul.completed', this.el).html('No user completed');
      }

      this.addAll();
    },

    addAll: function() {
      _.each(_.first(this.challengersObj.in_progress, this.limit), function(model){
        this.addOneInProgress(model);
      }, this);

      if((this.challengersObj.in_progress.length <= this.limit) || !this.limit){
        $('button.load-more-in-progress', this.el).addClass('hide');
      } else {
        $('button.load-more-in-progress', this.el).removeClass('hide');
      }

      _.each(_.first(this.challengersObj.completed, this.limit), function(model){
        this.addOneCompleted(model);
      }, this);

      if((this.challengersObj.completed.length <= this.limit) || !this.limit){
        $('button.load-more-completed', this.el).addClass('hide');
      } else {
        $('button.load-more-completed', this.el).removeClass('hide');
      }
    },

    addOneInProgress: function(model) {
      var challenger = new ChallengerItem({ model: model });
      $('ul.in-progress', this.el).append(challenger.render().el);
    },

    loadMoreInProgress: function() {
      this.challengersObj.in_progress = this.challengersObj.in_progress.slice(this.limit);
      this.addAll();
    },

    addOneCompleted: function(model) {
      var challenger = new ChallengerItem({ model: model });
      $('ul.completed', this.el).append(challenger.render().el);
    },

    loadMoreCompleted: function() {
      this.challengersObj.completed = this.challengersObj.completed.slice(this.limit);
      this.addAll();
    },

    clean: function() {
      this.remove();
      this.unbind();
      sandbox.models.challengerModel.unbind();
    }

  });
  return ChallengerList;
});
