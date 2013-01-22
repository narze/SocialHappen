define [
  'backbone'
  'text!templates/devices-template.html'
  'views/devices-filter-view'
  'views/pagination-view'
  'views/device-item-view'
  ], (Backbone, DevicesTemplate, DevicesFilterView, PaginationView, DeviceItemView) ->

  View = Backbone.View.extend

    id: 'devices-view'

    events:
      'click .sort-name': 'sort'
      'click .sort-data': 'sort'

    initialize: ->
      _.bindAll @
      @subViews = {}
      @collection.bind 'reset', @listDevices
      @collection.bind 'add', @listDevices
      @collection.bind 'remove', @listDevices
      @collection.fetch()

    listDevices: ->
      @$('#device-list').empty()
      @collection.each (model) ->
        @addDevice(model)
      , @

    addDevice: (model)->
      device = new DeviceItemView model: model
      @subViews['device-' + model.cid] = device
      @$('#device-list').append(device.render().el)

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
      else if $target.hasClass 'sort-data'
        @collection.sort = 'data'

      @collection.fetch()

    render: ->
      @$el.html DevicesTemplate
      @delegateEvents()
      @listDevices()

      # filter
      if !@subViews.filter
        @subViews.filter = new DevicesFilterView collection: @collection

      @$('.devices-filter-container').html @subViews.filter.render().el

      # pagination
      paginationCount = @$('.pagination-container').length
      if paginationCount
        if !@subViews.pagination
          @subViews.pagination = []
          for i in [0..paginationCount]
            @subViews.pagination[i] = new PaginationView collection: @collection
        for i in [0..paginationCount]
          @$('.pagination-container:eq(' + i + ')').html @subViews.pagination[i].render().el

      # device add form
      # if !@subViews['device-add']
      #   @subViews['device-add'] = new DeviceAddView model: new @collection.model

      # @$('#device-add-container').html @subViews['device-add'].render().el

      @rendered = true
      @

  View