define ['backbone'], (Backbone) ->
  Router = Backbone.Router.extend
    routes:
      '': 'users'
      'users': 'users'
      'activities': 'activities'
      '*other': 'badRoute'

    users: ->
      $('#content').html window.backend.Views.UsersView.render().el
      $('#sidebar-view .main-menu li').removeClass('active')
      $('#sidebar-view .main-menu li.users-tab-menu').addClass('active')

    activities: ->
      $('#content').html window.backend.Views.ActivitiesView.render().el
      $('#sidebar-view .main-menu li').removeClass('active')
      $('#sidebar-view .main-menu li.activities-tab-menu').addClass('active')

    badRoute: ->
      console.log '404 : Route not found'
      @notFound = true