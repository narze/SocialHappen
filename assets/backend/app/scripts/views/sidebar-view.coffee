define ['backbone'], (Backbone) ->
  View = Backbone.View.extend
    el: $('#app > #sidebar-view')
    initialize: ->
      @render()
    render: ->
      @$el.html 'sidebarview'
