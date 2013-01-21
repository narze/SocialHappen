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
  'moment'
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
  moment
) ->

  # Server specific config
  config =
    dev:
      baseUrl: 'https://socialhappen.dyndns.org/socialhappen/'
    beta:
      baseUrl: 'http://beta.socialhappen.com/'
    production:
      baseUrl: 'http://www.socialhappen.com/'

  if window.location.href.match(/beta\.socialhappen\.com/)
    env = 'beta'
  else if window.location.href.match(/(\.)?socialhappen\.com/)
    env = 'production'
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

  Backbone.emulateJSON = true
  Backbone.history.start()

  window.appLoaded = true

  window.checkSession = ->
    $.ajax
      url: window.baseUrl + 'apiv3/check_session'
      dataType: 'json'
      success: (resp) ->
        now = moment().format('MMMM Do YYYY, h:mm:ss a')
        if resp.success
          console.log 'Session check ok : ' + now + ' UserId : ' + resp.data
        else
          clearInterval window.checkSessionInterval
          if typeof mocha isnt 'function'
            alert 'Session Expired ' + now

  window.checkSessionInterval = setInterval window.checkSession, 10000