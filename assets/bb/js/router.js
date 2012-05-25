// Filename: router.js
define([
  'jquery',
  'underscore',
  'backbone',
	'vm'
], function ($, _, Backbone, Vm) {
  var AppRouter = Backbone.Router.extend({
    routes: {
      // Pages
      '/action/:id': 'challengeAction',
    
      // Default - catch all
      '*actions': 'defaultAction'
    }
  });

  var initialize = function(options){
		var appView = options.appView;
    var routerSet = options.routerSet;
    var router = new AppRouter(options);

    if(routerSet === 'player-challenge') {
      router.on('route:challengeAction', function (actionDataId) {
        window.ChallengeAction || (window.ChallengeAction = {});
        window.ChallengeAction.actionDataId = actionDataId;
        options.challengeActionModel.url =
          base_url + 'apiv3/challenge_action?action_data_id=' + actionDataId;
          
        options.challengeActionModel.fetch({success: function() {
          require(['views/player/challenge-action'], function (ChallengeActionView) {
            var challengeActionView = Vm.create(appView, 'ChallengeActionView', ChallengeActionView, {
              challengeActionModel: options.challengeActionModel,
              vent: options.vent
            });
            challengeActionView.render();
          });
        }});
      });
    }

    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
