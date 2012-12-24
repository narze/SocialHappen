define ['backbone'], (Backbone) ->
  Router = Backbone.Router.extend
    routes:
      '': 'index'
      'users': 'users'
      'activities': 'activities'
      '*other': 'badRoute'

    users: ->
      window.backend.Views.UsersView.render()

    activities: ->
      window.backend.Views.ActivitiesView.render()

    badRoute: ->
      console.log '404 : Route not found'
      @notFound = true