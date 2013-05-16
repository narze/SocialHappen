// Generated by CoffeeScript 1.6.2
define(['main', 'routes/main-router', 'views/main-view', 'views/users-view', 'views/activities-view', 'views/companies-view', 'views/challenges-view', 'views/rewards-view', 'views/devices-view', 'views/reward-machines-view', 'models/company-model', 'collections/company-collection', 'models/user-model', 'collections/user-collection', 'models/activity-model', 'collections/activity-collection', 'models/challenge-model', 'collections/challenge-collection', 'models/reward-model', 'collections/reward-collection', 'models/device-model', 'collections/device-collection', 'models/branch-model', 'collections/branch-collection', 'models/reward-machine-model', 'collections/reward-machine-collection'], function(Main, MainRouter, MainView, UsersView, ActivitiesView, CompaniesView, ChallengesView, RewardsView, DevicesView, RewardMachinesView, CompanyModel, CompanyCollection, UserModel, UserCollection, ActivityModel, ActivityCollection, ChallengeModel, ChallengeCollection, RewardModel, RewardCollection, DeviceModel, DeviceCollection, BranchModel, BranchCollection, RewardMachineModel, RewardMachineCollection) {
  var config, env;

  config = {
    dev: {
      baseUrl: 'http://socialhappen.dyndns.org/socialhappen/'
    },
    beta: {
      baseUrl: 'https://beta.socialhappen.com/'
    },
    production: {
      baseUrl: 'http://www.socialhappen.com/'
    }
  };
  if (window.location.href.match(/beta\.socialhappen\.com/)) {
    env = 'beta';
  } else if (window.location.href.match(/(\.)?socialhappen\.com/)) {
    env = 'production';
    console.log = function() {};
  } else {
    env = 'dev';
  }
  window.baseUrl = config[env].baseUrl;
  window.backend.Routers.MainRouter = new MainRouter;
  window.backend.Models.CompanyModel = CompanyModel;
  window.backend.Collections.CompanyCollection = new CompanyCollection;
  window.backend.Models.UserModel = UserModel;
  window.backend.Collections.UserCollection = new UserCollection;
  window.backend.Models.ActivityModel = ActivityModel;
  window.backend.Collections.ActivityCollection = new ActivityCollection;
  window.backend.Models.ChallengeModel = ChallengeModel;
  window.backend.Collections.ChallengeCollection = new ChallengeCollection;
  window.backend.Models.RewardModel = RewardModel;
  window.backend.Collections.RewardCollection = new RewardCollection;
  window.backend.Models.DeviceModel = DeviceModel;
  window.backend.Collections.DeviceCollection = new DeviceCollection;
  window.backend.Models.BranchModel = BranchModel;
  window.backend.Collections.BranchCollection = new BranchCollection;
  window.backend.Models.RewardMachineModel = RewardMachineModel;
  window.backend.Collections.RewardMachineCollection = new RewardMachineCollection;
  window.backend.Views.MainView = new MainView;
  window.backend.Views.MainView.render();
  window.backend.Views.UsersView = new UsersView({
    collection: window.backend.Collections.UserCollection
  });
  window.backend.Views.ActivitiesView = new ActivitiesView({
    collection: window.backend.Collections.ActivityCollection
  });
  window.backend.Views.CompaniesView = new CompaniesView({
    collection: window.backend.Collections.CompanyCollection
  });
  window.backend.Views.ChallengesView = new ChallengesView({
    collection: window.backend.Collections.ChallengeCollection
  });
  window.backend.Views.RewardsView = new RewardsView({
    collection: window.backend.Collections.RewardCollection
  });
  window.backend.Views.DevicesView = new DevicesView({
    collection: window.backend.Collections.DeviceCollection
  });
  window.backend.Views.RewardMachinesView = new RewardMachinesView({
    collection: window.backend.Collections.RewardMachineCollection
  });
  Backbone.emulateJSON = true;
  Backbone.emulateHTTP = true;
  Backbone.history.start();
  window.appLoaded = true;
  window.checkSession = function() {
    return $.ajax({
      url: window.baseUrl + 'apiv3/check_admin_session',
      dataType: 'json',
      success: function(resp) {
        if (resp.success) {
          return console.log('Session check ok');
        } else {
          clearInterval(window.checkSessionInterval);
          if (typeof mocha !== 'function') {
            alert(resp.data);
            if (resp.code === 403) {
              window.location.href = window.baseUrl;
              return;
            }
            return window.location.href = window.baseUrl;
          }
        }
      }
    });
  };
  window.checkSession();
  return window.checkSessionInterval = setInterval(window.checkSession, 10000);
});
