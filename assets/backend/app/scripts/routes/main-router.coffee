define ['backbone'], (Backbone) ->
  Router = Backbone.Router.extend
    routes:
      '': 'index'
      'users': 'users'
      'activities': 'activities'
      '*other': 'badRoute'

    users: ->
      $('#content').html window.backend.Views.UsersView.render().el

    activities: ->
      $('#content').html window.backend.Views.ActivitiesView.render().el

    badRoute: ->
      console.log '404 : Route not found'
      @notFound = true