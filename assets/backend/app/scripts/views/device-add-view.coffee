define [
  'backbone'
  'text!templates/device-add-template.html'
  'backboneValidationBootstrap'
  'moment'
  'jqueryPlugins/jquery.chosen.min'
  ], (Backbone, DeviceAddTemplate, BackboneValidationBootstrap, mm, jqform, chosen) ->

  View = Backbone.View.extend

    id: 'device-add-view'

    events:
      'submit form.device-add-form': 'addNewDevice'
      'click .box-header': 'minimize'
      'change #device-add-company': 'fetchBranches'

    initialize: ->
      _.bindAll @
      @model = new window.backend.Models.DeviceModel
      Backbone.Validation.bind @

      @companyCollection = _.clone window.backend.Collections.CompanyCollection
      @companyCollection.bind 'reset', @listCompanies
      @companyCollection.fetch()

      @branchCollection = _.clone window.backend.Collections.BranchCollection
      @branchCollection.bind 'reset', @listBranches

    minimize: (e) ->
      e.preventDefault()
      $target = @$el.find '.box-content'

      if $target.is ':visible'
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down')
      else
        @listCompanies()
        @$('[data-rel="chosen"],[rel="chosen"]').chosen()
        @$('.box-header .btn-minimize i').removeClass('icon-chevron-down').addClass('icon-chevron-up')

      $target.slideToggle()

      @$("form :input:visible:enabled:first").focus()

    addNewDevice: (e) ->
      e.preventDefault()

      newDevice =
        id: @$('#device-add-id').val()
        title: @$('#device-add-title').val()
        data: @$('#device-add-data').val()
        company: @$('#device-add-company').val()
        branch: @$('#device-add-branch').val()
        status: 'pending'
        created_at: (new Date()).getTime() / 1000 << 0
        installed_at: null
        info: {}

      console.log(newDevice);
      # Hardcoded : just validate & save!
      if @model.set newDevice
        @model.save null, success: =>
          window.backend.Collections.DeviceCollection.add @model.clone()
          @render()

    listCompanies: () ->
      @$('#device-add-company').html('<option value="">Select Company</option>')

      @companyCollection.each (model) =>
        @$('#device-add-company').append '<option value="' +
          model.get('company_id') +
          '">' +
          model.get('company_name') +
          '</option>'

      @$('#device-add-company').trigger "liszt:updated"

    fetchBranches: () ->
      companyId = @$('#device-add-company').val()
      @branchCollection.filter =
        company_id: companyId

      # TODO : don't limit to 20 (pagination)
      @branchCollection.fetch()

    listBranches: () ->
      @$('#device-add-branch').html('<option value="">Select Branch</option>')

      @$('#device-add-branch').html '<option value="">No Branch</option>' unless @branchCollection.models.length

      @branchCollection.each (model) =>
        console.log(model.attributes);
        @$('#device-add-branch').append '<option value="' +
          model.id +
          '">' +
          model.get('title') +
          '</option>'

      @$('#device-add-branch').trigger "liszt:updated"


    render: ->
      @$el.html _.template DeviceAddTemplate, {}
      @delegateEvents()

      @$('.datepicker').datepicker() if @$('.datepicker')

      @rendered = true
      @

  View