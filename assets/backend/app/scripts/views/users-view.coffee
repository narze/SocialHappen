define [
  'backbone'
  'text!templates/users-template.html'
  'views/users-filter-view'
  'views/pagination-view'
  'views/user-item-view'
  ], (Backbone, UsersTemplate, UsersFilterView, PaginationView, UserItemView) ->

  View = Backbone.View.extend

    id: 'users-view'

    events:
      'click .sort-name': 'sort'
      'click .sort-signup-date': 'sort'
      'click .sort-last-seen': 'sort'
      'click .sort-points': 'sort'

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

    sort: (e) ->
      e.preventDefault()

      $target = $(e.currentTarget)

      if $target.hasClass 'sort-asc'
        $target.removeClass 'sort-asc'
        $target.addClass 'sort-desc'
        $target.removeClass('icon-chevron-up').addClass('icon-chevron-down')
        @collection.order = '-'
      else
        $target.removeClass 'sort-desc'
        $target.addClass 'sort-asc'
        $target.removeClass('icon-chevron-down').addClass('icon-chevron-up')
        @collection.order = '+'

      if $target.hasClass 'sort-name'
        @collection.sort = 'user_first_name'
      else if $target.hasClass 'sort-signup-date'
        @collection.sort = 'user_register_date'
      else if $target.hasClass 'sort-last-seen'
        @collection.sort = 'user_last_seen'
      else if $target.hasClass 'sort-points'
        @collection.sort = 'points'

      @collection.fetch()

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