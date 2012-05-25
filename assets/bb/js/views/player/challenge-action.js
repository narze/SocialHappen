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
      var templates = {
        201: 'text!templates/player/qr-form.html',
        202: 'text!templates/player/feedback-form.html',
        203: 'text!templates/player/checkin-form.html'

      }

      formTemplateName = templates[challengeAction.action_id]
      require([formTemplateName], function (formTemplate) {
        formTemplate = _.template(formTemplate);
        challengeAction.data.hash = challengeAction.hash;
        $('.criteria-form:visible').slideUp();
        $('.criteria-form[data-id="'+ challengeAction._id.$id +'"]').html(formTemplate(challengeAction.data)).slideDown();
      });
		}
	});
  return AppView;
});
