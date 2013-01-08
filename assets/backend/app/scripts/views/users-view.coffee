define [
  'backbone'
  'text!templates/users-template.html'
  'views/user-item-view'
  ], (Backbone, UsersTemplate, UserItemView) ->

  View = Backbone.View.extend

    id: 'users-view'

    events:
      # pagination
      'click a.servernext': 'nextResultPage'
      'click a.serverprevious': 'previousResultPage'
      # 'click a.orderUpdate': 'updateSortBy'
      'click a.serverlast': 'gotoLast'
      'click a.page': 'gotoPage'
      'click a.serverfirst': 'gotoFirst'
      # 'click a.serverpage': 'gotoPage'

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

      @pagination()

    addUser: (model)->
      user = new UserItemView model: model
      @subViews['user-' + model.cid] = user
      @$('#user-list').append(user.render().el)

    pagination: ->
      @$('.users-pagination').html \
        _.template \
          @$('#users-pagination-template').html(),
          @collection.info()

    nextResultPage: (e) ->
      e.preventDefault()
      @collection.requestNextPage()

    previousResultPage: (e) ->
      e.preventDefault()
      @collection.requestPreviousPage()

    gotoFirst: (e) ->
      e.preventDefault()
      @collection.goTo(@collection.information.firstPage)

    gotoLast: (e) ->
      e.preventDefault()
      @collection.goTo(@collection.information.lastPage)

    gotoPage: (e) ->
      e.preventDefault()
      page = $(e.target).text()
      @collection.goTo(page)

    render: ->
      @$el.html UsersTemplate
      @delegateEvents()
      @listUsers()
      @rendered = true
      @

  View