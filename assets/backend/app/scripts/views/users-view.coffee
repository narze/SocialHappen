define [
  'backbone'
  'text!templates/users-template.html'
  'views/pagination-view'
  'views/user-item-view'
  ], (Backbone, UsersTemplate, PaginationView, UserItemView) ->

  View = Backbone.View.extend

    id: 'users-view'

    initialize: ->
      _.bindAll @
      @subViews = {}
      @collection.bind 'reset', @listUsers
      @collection.bind 'change', @listUsers
      @collection.fetch()

    listUsers: ->
      @$('#user-list').empty()
      @collection.each (model) ->
        @addUser(model)
      , @

    addUser: (model)->
      user = new UserItemView model: model
      @subViews['user-' + model.cid] = user
      @$('#user-list').append(user.render().el)

    render: ->
      @$el.html UsersTemplate
      @delegateEvents()
      @listUsers()

      # pagination
      if !@subViews.pagination
        @subViews.pagination = []
        @subViews.pagination[0] = new PaginationView collection: @collection
        @subViews.pagination[1] = new PaginationView collection: @collection
      @$('.pagination-container:eq(0)').html @subViews.pagination[0].render().el
      @$('.pagination-container:eq(1)').html @subViews.pagination[1].render().el

      @rendered = true
      @

  View