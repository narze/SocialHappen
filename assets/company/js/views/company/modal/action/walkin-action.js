define([
  'jquery',
  'underscore',
  'backbone',
  'views/company/modal/action/walkin-form',
  'text!templates/company/modal/action/WalkinActionTemplate.html'
], function($, _, Backbone, WalkinFormView, WalkinActionTemplate){
  var WalkinAddView = Backbone.View.extend({

    walkinActionTemplate: _.template(WalkinActionTemplate),
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
          var self = this;

          this.model.save({}, {
            success: function(){
              self.options.vent.trigger('showEditModal', self.model);
            }
          });
        }
      }
    },

    render: function () {
      var data = this.options.action;
      data.sonar_code = this.model.get('sonar_frequency');
      branch_sonar_data = this.model.get('branch_sonar_data') || []
      data.sonar_code = (data.sonar_code ? [data.sonar_code] : []).concat(branch_sonar_data).join()

      $(this.el).html(this.walkinActionTemplate(data));
      return this;
    },

    showEdit: function(){
      var walkinFormView = new WalkinFormView({
        model: this.model,
        action: this.options.action,
        vent: this.options.vent,
        triggerModal: this.options.triggerModal,
        save: this.options.save
      });
      $('#action-modal').html(walkinFormView.render().el);
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
  return WalkinAddView;
});
