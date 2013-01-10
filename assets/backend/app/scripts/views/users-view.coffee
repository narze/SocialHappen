define [
  'backbone'
  'text!templates/users-template.html'
  'views/users-filter-view'
  'views/pagination-view'
  'views/user-item-view'
  ], (Backbone, UsersTemplate, UsersFilterView, PaginationView, UserItemView) ->

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

      # filter
      if !@subViews.filter
        @subViews.filter = new UsersFilterView collection: @collection

      @$('.users-filter-container').html @subViews.filter.render().el

      # pagination
      paginationCount = @$('.pagination-container').length
      if paginationCount
        if !@subViews.pagination
          @subViews.pagination = []
          for i in [0..paginationCount]
            @subViews.pagination[i] = new PaginationView collection: @collection
        for i in [0..paginationCount]
          @$('.pagination-container:eq(' + i + ')').html @subViews.pagination[i].render().el

      @rendered = true
      @

  View