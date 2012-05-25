require([
  'router',
  'vm',
  'events',
  'shplainbar',
  'models/challenge-action'
], function(Router, Vm, vent, Shplainbar, ChallengeActionModel){
  
  var challengeActionModel = new ChallengeActionModel();
  // The router now has a copy of all main appview
  Router.initialize({
    vent: vent,
    routerSet: 'player-challenge',
    challengeActionModel: challengeActionModel
  });
});
