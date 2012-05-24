require([
  'views/settings/challenge',
  'router',
  'vm',
  'events',
  'shplainbar'
], function(AppView, Router, Vm, vent, Shplainbar){
  
  var appView = Vm.create({
    vent: vent
  }, 'AppView', AppView);
  appView.render();
  
  // The router now has a copy of all main appview
  Router.initialize({
    appView: appView,
    vent: vent
  });
});
