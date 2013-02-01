define ['backbone'], (Backbone) ->
  Router = Backbone.Router.extend
    routes:
      '': 'signin'
      '*other': 'badRoute'

    signin: ->
      $('#content').html window.backend.Views.SigninView.render().el
      $('#sidebar-view .main-menu li').removeClass('active')
      $('#sidebar-view .main-menu li.users-tab-menu').addClass('active')

    badRoute: ->
      console.log '404 : Route not found'
      @notFound = true