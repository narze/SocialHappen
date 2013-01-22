define [
  'backbone'
  'text!templates/rewards-template.html'
  'views/rewards-filter-view'
  'views/pagination-view'
  'views/reward-item-view'
  'views/reward-add-view'
  ], (Backbone, RewardsTemplate, RewardsFilterView, PaginationView, RewardItemView, RewardAddView) ->

  View = Backbone.View.extend

    id: 'rewards-view'

    events:
      'click .sort-name': 'sort'
      'click .sort-point-required': 'sort'
      'click .sort-amount': 'sort'
      'click .sort-amount-redeemed': 'sort'
      'click .sort-can-play-once': 'sort'

    initialize: ->
      _.bindAll @
      @subViews = {}
      @collection.bind 'reset', @listRewards
      @collection.bind 'add', @listRewards
      @collection.bind 'remove', @listRewards
      @collection.fetch()

    listRewards: ->
      @$('#reward-list').empty()
      @collection.each (model) ->
        @addReward(model)
      , @

    addReward: (model)->
      reward = new RewardItemView model: model
      @subViews['reward-' + model.cid] = reward
      @$('#reward-list').append(reward.render().el)

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
        @collection.sort = 'name'
      else if $target.hasClass 'sort-point-required'
        @collection.sort = 'point'
      else if $target.hasClass 'sort-amount'
        @collection.sort = 'amount'
      else if $target.hasClass 'sort-amount-redeemed'
        @collection.sort = 'amount_redeemed'
      else if $target.hasClass 'sort-can-play-once'
        @collection.sort = 'once'

      @collection.fetch()

    render: ->
      @$el.html RewardsTemplate
      @delegateEvents()
      @listRewards()

      # filter
      if !@subViews.filter
        @subViews.filter = new RewardsFilterView collection: @collection

      @$('.rewards-filter-container').html @subViews.filter.render().el

      # pagination
      paginationCount = @$('.pagination-container').length
      if paginationCount
        if !@subViews.pagination
          @subViews.pagination = []
          for i in [0..paginationCount]
            @subViews.pagination[i] = new PaginationView collection: @collection
        for i in [0..paginationCount]
          @$('.pagination-container:eq(' + i + ')').html @subViews.pagination[i].render().el

      # reward add form
      if !@subViews['reward-add']
        @subViews['reward-add'] = new RewardAddView model: new @collection.model

      @$('#reward-add-container').html @subViews['reward-add'].render().el

      @rendered = true
      @

  View