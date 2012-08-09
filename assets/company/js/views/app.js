define([
  'jquery',
  'underscore',
  'backbone',
  'vm',
	'events',
  'text!templates/layout.html',
  'sandbox'
], function($, _, Backbone, Vm, Events, layoutTemplate, sandbox){
  var AppView = Backbone.View.extend({
    el: '#content',
    initialize: function () {

    },
    render: function () {
			var self = this;
      // $(this.el).html(layoutTemplate);
      require(['views/header/navigation'], function (HeaderNavigationView) {
        var headerNavigationView = Vm.create(self, 'HeaderNavigationView', HeaderNavigationView, {
          currentUserModel: sandbox.models.currentUserModel
        });
        headerNavigationView.render();
      });
		}
	});
  return AppView;
});
