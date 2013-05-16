define [
  'backbone'
  'text!templates/sonar-code-add-template.html'
  'backboneValidationBootstrap'
  'moment'
  'jqueryPlugins/jquery.chosen.min'
  ], (Backbone, SonarCodeAddTemplate, BackboneValidationBootstrap, mm, jqform, chosen) ->

  View = Backbone.View.extend

    id: 'sonar-code-add-view'

    events:
      'submit form.sonar-code-add-form': 'addNewSonarCode'
      'click .box-header': 'minimize'
      'change #sonar-code-add-company': 'fetchBranches'

    initialize: ->
      _.bindAll @
      @model = new window.backend.Models.SonarCodeModel
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

    addNewSonarCode: (e) ->
      e.preventDefault()

      newSonarCode =
        id: @$('#sonar-code-add-id').val()
        title: @$('#sonar-code-add-title').val()
        data: @$('#sonar-code-add-data').val()
        company: @$('#sonar-code-add-company').val()
        branch: @$('#sonar-code-add-branch').val()
        status: 'pending'
        created_at: (new Date()).getTime() / 1000 << 0
        installed_at: null
        info: {}

      console.log(newSonarCode);
      # Hardcoded : just validate & save!
      if @model.set newSonarCode
        @model.save null, success: =>
          window.backend.Collections.SonarCodeCollection.add @model.clone()
          @render()

    listCompanies: () ->
      @$('#sonar-code-add-company').html('<option value="">Select Company</option>')

      @companyCollection.each (model) =>
        @$('#sonar-code-add-company').append '<option value="' +
          model.get('company_id') +
          '">' +
          model.get('company_name') +
          '</option>'

      @$('#sonar-code-add-company').trigger "liszt:updated"

    fetchBranches: () ->
      companyId = @$('#sonar-code-add-company').val()
      @branchCollection.filter =
        company_id: companyId

      # TODO : don't limit to 20 (pagination)
      @branchCollection.fetch()

    listBranches: () ->
      @$('#sonar-code-add-branch').html('<option value="">Select Branch</option>')

      @$('#sonar-code-add-branch').html '<option value="">No Branch</option>' unless @branchCollection.models.length

      @branchCollection.each (model) =>
        console.log(model.attributes);
        @$('#sonar-code-add-branch').append '<option value="' +
          model.id +
          '">' +
          model.get('title') +
          '</option>'

      @$('#sonar-code-add-branch').trigger "liszt:updated"


    render: ->
      @$el.html _.template SonarCodeAddTemplate, {}
      @delegateEvents()

      @$('.datepicker').datepicker() if @$('.datepicker')

      @rendered = true
      @

  View