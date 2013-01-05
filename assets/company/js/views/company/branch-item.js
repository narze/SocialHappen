define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/branch-item.html'
], function($, _, Backbone, branchItemTemplate){
  var BranchItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'item',
    branchItemTemplate: _.template(branchItemTemplate),
    events: {
      'click a.branch': 'showEdit',
      'click': 'showEdit'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('change', this.render);
      this.model.bind('destroy', this.remove);
    },
    render: function () {
      console.log('render branch item');
      var data = this.model.toJSON();
      data.baseUrl = window.Company.BASE_URL;
      $(this.el).html(this.branchItemTemplate(data));
      return this;
    },

    showEdit: function(e){
      e.preventDefault();
      console.log('show branch edit modal', this.model);
      this.options.vent.trigger('showEditBranchModal', this.model);
    }
  });
  return BranchItemView;
});
