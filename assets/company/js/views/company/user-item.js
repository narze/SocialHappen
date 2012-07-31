define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/user-item.html',
  'text!templates/company/user-modal.html',
  'timeago'
], function($, _, Backbone, userItemTemplate, userModalTemplate, timeago){
  var CompanyUserItem = Backbone.View.extend({
    className: 'user-item row-fluid',
    userItemTemplate: _.template(userItemTemplate),
    userModalTemplate: _.template(userModalTemplate),
    events: {
      'click .user-view ': 'viewUserModal'
    },
    initialize: function(){
      _.bindAll(this);
      this.model.bind('view', this.viewUserModal);
    },
    render: function () {
      var data = this.model.toJSON();
      $(this.el).html(this.userItemTemplate(data));
      return this;
    },
    viewUserModal: function(e) {
      // if(e) { e.preventDefault(); }
      var data = this.model.toJSON(),
        self = this;
      $('#user-modal').html(self.userModalTemplate(data)).modal('show');
    }
  });
  return CompanyUserItem;
});
