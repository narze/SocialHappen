define([
  'jquery',
  'underscore',
  'backbone',
  'views/company/modal/action/like-form',
  'text!templates/company/modal/action/LikeActionTemplate.html'
], function($, _, Backbone, LikeFormView, LikeActionTemplate){
  var LikeAddView = Backbone.View.extend({

    likeActionTemplate: _.template(LikeActionTemplate),
    tagName: 'li',

    events: {
      'click .edit-action': 'showEdit',
      'click .remove-action': 'remove'
    },

    initialize: function(){
      _.bindAll(this);

      //Add action into model
      if(this.options.add) {
        var criteria = this.model.get('criteria');

        criteria.push(this.options.action);

        this.model.set('criteria', criteria).trigger('change');
        if(this.options.save){
          this.model.save();
        }
      }
    },

    render: function () {
      var data = this.options.action;

      $(this.el).html(this.likeActionTemplate(data));
      return this;
    },

    showEdit: function(){
      var likeFormView = new LikeFormView({
        model: this.model,
        action: this.options.action,
        vent: this.options.vent,
        triggerModal: this.options.triggerModal,
        save: this.options.save
      });
      $('#action-modal').html(likeFormView.render().el);
      $('#action-modal').modal('show');
    },

    remove: function(e) {
      e.preventDefault();
      var criteria = this.model.get('criteria');
      var removeIndex = $(e.currentTarget).parents('ul.criteria-list > li').index();
      delete criteria[removeIndex];
      criteria = _.compact(criteria);

      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
  });
  return LikeAddView;
});
