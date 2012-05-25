require([
  'views/player/challenge-list',
  'router',
  'vm',
  'events',
  'shplainbar',
  'models/challenge-action'
], function(PlayerChallengeListView, Router, Vm, vent, Shplainbar, ChallengeActionModel){
  
  var appView = Vm.create({
    vent: vent
  }, 'PlayerChallengeListView', PlayerChallengeListView);
  appView.render();
  
  var challengeActionModel = new ChallengeActionModel();
  // The router now has a copy of all main appview
  Router.initialize({
    appView: appView,
    vent: vent,
    routerSet: 'player-challenge',
    challengeActionModel: challengeActionModel
  });
});
