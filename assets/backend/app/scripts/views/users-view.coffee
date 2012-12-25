define [
  'backbone'
  'text!templates/users-template.html'
  'views/user-item-view'
  ], (Backbone, UsersTemplate, UserItemView) ->

  View = Backbone.View.extend

    id: 'users-view'

    initialize: ->
      _.bindAll @
      @collection.bind 'reset', @listUsers
      @collection.fetch()

    listUsers: ->
      @collection.each (model) ->
        @addUser(model)
      , @

    addUser: (model)->
      user = new UserItemView
        model: model
      @$('#user-list').append(user.render().el)

    render: ->
      @$el.html UsersTemplate
      @listUsers()
      @rendered = true
      @

  View