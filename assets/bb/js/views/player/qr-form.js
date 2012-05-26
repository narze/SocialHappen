define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/player/qr-form.html'
], function($, _, Backbone, formTemplate){
  var AppView = Backbone.View.extend({
    formTemplate: _.template(formTemplate),
    events: {
      'click button.submit': 'formSubmit'
    },
    initialize: function () {
      _.bindAll(this);
    },
    render: function () {
			$(this.el).html(this.formTemplate(this.options.challengeAction));
      $(this.el).slideDown();
		},
    formSubmit: function() {

    }
	});
  return AppView;
});
