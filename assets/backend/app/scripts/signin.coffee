define [
  'main'
  'routes/signin-router'
  'views/signin-view'
], (
  Main
  SigninRouter
  SigninView
) ->

  # Server specific config
  config =
    dev:
      baseUrl: 'https://socialhappen.dyndns.org/socialhappen/'
    beta:
      baseUrl: 'https://beta.socialhappen.com/'
    production:
      baseUrl: 'http://www.socialhappen.com/'

  if window.location.href.match(/beta\.socialhappen\.com/)
    env = 'beta'
  else if window.location.href.match(/(\.)?socialhappen\.com/)
    env = 'production'
  else
    env = 'dev'

  window.baseUrl = config[env].baseUrl

  window.backend.Routers.SigninRouter = new SigninRouter

  window.backend.Views.SigninView = new SigninView
  window.backend.Views.SigninView.render()

  Backbone.emulateJSON = true
  Backbone.history.start()

  window.appLoaded = true
