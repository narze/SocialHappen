define([
  'jquery',
  'underscore',
  'backbone',
  'vm',
	'events'
], function($, _, Backbone, Vm, Events){
  var AppView = Backbone.View.extend({

    initialize: function () {
      _.bindAll(this);
    },
    render: function () {
			var that = this;
      var challengeAction = this.options.challengeActionModel.attributes;
      var actionId, formTemplateName;
      if(challengeAction.action_id) {
        actionId = challengeAction.action_id;
        if(actionId === 201) {
          console.log('QR');
          formTemplateName = 'text!templates/player/qr-form.html';
        } else if (actionId === 202) {
          console.log('Feedback');
          formTemplateName = 'text!templates/player/feedback-form.html';
        } else if (actionId === 203) {
          console.log('Checkin');
          formTemplateName = 'text!templates/player/checkin-form.html';
        }
      }

      require([formTemplateName], function (formTemplate) {
        formTemplate = _.template(formTemplate);
        challengeAction.data.hash = challengeAction.hash;
        $('.criteria-form').html(formTemplate(challengeAction.data));
      });
		}
	});
  return AppView;
});
