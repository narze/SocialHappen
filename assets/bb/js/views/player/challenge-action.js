define([
  'jquery',
  'underscore',
  'backbone'
], function($, _, Backbone){
  var AppView = Backbone.View.extend({

    events: {

    },
    initialize: function () {
      _.bindAll(this);
    },
    render: function () {
			var self = this;
      var challengeAction = this.options.challengeActionModel.attributes;
      var formViewName;
      var views = {
        201: 'views/player/qr-form',
        202: 'views/player/feedback-form',
        203: 'views/player/checkin-form'
      };

      $('.criteria-form').slideUp();
      formViewName = views[challengeAction.action_id];
      require([formViewName], function(FormView) {
        var formView = new FormView({
          challengeAction: challengeAction,
          el: $('.criteria-form[data-id="'+ self.options.challengeActionModel.id +'"]'),
          vent: self.options.vent
        });
        formView.render();
      });
		}
	});
  return AppView;
});
