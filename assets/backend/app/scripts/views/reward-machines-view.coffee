define [
  'backbone'
  'text!templates/reward-machines-template.html'
  'views/reward-machines-filter-view'
  'views/pagination-view'
  'views/reward-machine-item-view'
  'views/reward-machine-add-view'
  ], (Backbone, RewardMachinesTemplate, RewardMachinesFilterView, PaginationView, RewardMachineItemView, RewardMachineAddView) ->

  View = Backbone.View.extend

    id: 'reward-machines-view'

    events:
      'click .sort-name': 'sort'
      'click .sort-id': 'sort'

    initialize: ->
      _.bindAll @
      @subViews = {}
      @collection.bind 'reset', @listRewardMachines
      @collection.bind 'add', @listRewardMachines
      @collection.bind 'remove', @listRewardMachines
      @collection.fetch()

    listRewardMachines: ->
      @$('#reward-machine-list').empty()
      @collection.each (model) ->
        @addRewardMachine(model)
      , @

    addRewardMachine: (model)->
      rewardMachine = new RewardMachineItemView model: model
      @subViews['reward-machine-' + model.cid] = rewardMachine
      @$('#reward-machine-list').append(rewardMachine.render().el)

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
      else if $target.hasClass 'sort-id'
        @collection.sort = '_id'

      @collection.fetch()

    render: ->
      @$el.html RewardMachinesTemplate
      @delegateEvents()
      @listRewardMachines()

      # filter
      if !@subViews.filter
        @subViews.filter = new RewardMachinesFilterView collection: @collection

      @$('.reward-machines-filter-container').html @subViews.filter.render().el

      # pagination
      paginationCount = @$('.pagination-container').length
      if paginationCount
        if !@subViews.pagination
          @subViews.pagination = []
          for i in [0..paginationCount]
            @subViews.pagination[i] = new PaginationView collection: @collection
        for i in [0..paginationCount]
          @$('.pagination-container:eq(' + i + ')').html @subViews.pagination[i].render().el

      # reward machine add form
      if !@subViews['reward-machine-add']
        @subViews['reward-machine-add'] = new RewardMachineAddView model: new @collection.model

      @$('#reward-machine-add-container').html @subViews['reward-machine-add'].render().el

      @rendered = true
      @

  View