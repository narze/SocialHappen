define [
  'main'
  'routes/main-router'
  'views/main-view'
  'views/users-view'
  'views/activities-view'
  'views/companies-view'
  'views/challenges-view'
  'views/rewards-view'
  'views/devices-view'
  'views/sonar-codes-view'
  'views/reward-machines-view'
  'models/company-model'
  'collections/company-collection'
  'models/user-model'
  'collections/user-collection'
  'models/activity-model'
  'collections/activity-collection'
  'models/challenge-model'
  'collections/challenge-collection'
  'models/reward-model'
  'collections/reward-collection'
  'models/device-model'
  'collections/device-collection'
  'models/sonar-code-model'
  'collections/sonar-code-collection'
  'models/branch-model'
  'collections/branch-collection'
  'models/reward-machine-model'
  'collections/reward-machine-collection'
], (
  Main
  MainRouter
  MainView
  UsersView
  ActivitiesView
  CompaniesView
  ChallengesView
  RewardsView
  DevicesView
  SonarCodesView
  RewardMachinesView
  CompanyModel
  CompanyCollection
  UserModel
  UserCollection
  ActivityModel
  ActivityCollection
  ChallengeModel
  ChallengeCollection
  RewardModel
  RewardCollection
  DeviceModel
  DeviceCollection
  SonarCodeModel
  SonarCodeCollection
  BranchModel
  BranchCollection
  RewardMachineModel
  RewardMachineCollection
) ->

  # Server specific config
  config =
    dev:
      baseUrl: 'http://socialhappen.dyndns.org/socialhappen/'
    beta:
      baseUrl: 'https://beta.socialhappen.com/'
    production:
      baseUrl: 'http://www.socialhappen.com/'

  if window.location.href.match(/beta\.socialhappen\.com/)
    env = 'beta'
  else if window.location.href.match(/(\.)?socialhappen\.com/)
    env = 'production'
    console.log = -> # disable logging
  else
    env = 'dev'

  window.baseUrl = config[env].baseUrl

  window.backend.Routers.MainRouter = new MainRouter

  window.backend.Models.CompanyModel = CompanyModel

  window.backend.Collections.CompanyCollection = new CompanyCollection

  window.backend.Models.UserModel = UserModel

  window.backend.Collections.UserCollection = new UserCollection

  window.backend.Models.ActivityModel = ActivityModel

  window.backend.Collections.ActivityCollection = new ActivityCollection

  window.backend.Models.ChallengeModel = ChallengeModel

  window.backend.Collections.ChallengeCollection = new ChallengeCollection

  window.backend.Models.RewardModel = RewardModel

  window.backend.Collections.RewardCollection = new RewardCollection

  window.backend.Models.DeviceModel = DeviceModel

  window.backend.Collections.DeviceCollection = new DeviceCollection

  window.backend.Models.SonarCodeModel = SonarCodeModel

  window.backend.Collections.SonarCodeCollection = new SonarCodeCollection

  window.backend.Models.BranchModel = BranchModel

  window.backend.Collections.BranchCollection = new BranchCollection

  window.backend.Models.RewardMachineModel = RewardMachineModel

  window.backend.Collections.RewardMachineCollection = new RewardMachineCollection

  window.backend.Views.MainView = new MainView
  window.backend.Views.MainView.render()

  window.backend.Views.UsersView = new UsersView
    collection: window.backend.Collections.UserCollection

  window.backend.Views.ActivitiesView = new ActivitiesView
    collection: window.backend.Collections.ActivityCollection

  window.backend.Views.CompaniesView = new CompaniesView
    collection: window.backend.Collections.CompanyCollection

  window.backend.Views.ChallengesView = new ChallengesView
    collection: window.backend.Collections.ChallengeCollection

  window.backend.Views.RewardsView = new RewardsView
    collection: window.backend.Collections.RewardCollection

  window.backend.Views.DevicesView = new DevicesView
    collection: window.backend.Collections.DeviceCollection

  window.backend.Views.SonarCodesView = new SonarCodesView
    collection: window.backend.Collections.SonarCodeCollection

  window.backend.Views.RewardMachinesView = new RewardMachinesView
    collection: window.backend.Collections.RewardMachineCollection

  Backbone.emulateJSON = true
  Backbone.emulateHTTP = true

  Backbone.history.start()

  window.appLoaded = true

  window.checkSession = ->
    # if !window.location.href.match(/^https?:\/\/(socialhappen\.dyndns\.org|localhost)/)
    $.ajax
      url: window.baseUrl + 'apiv3/check_admin_session'
      dataType: 'json'
      success: (resp) ->
        if resp.success
          console.log 'Session check ok'
        else
          clearInterval window.checkSessionInterval
          if typeof mocha isnt 'function'
            alert resp.data
            if resp.code is 403
              window.location.href = window.baseUrl
              return
            window.location.href = window.baseUrl # + 'login?next=backendv2'

  window.checkSession()
  window.checkSessionInterval = setInterval window.checkSession, 10000