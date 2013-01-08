define ['backbone', 'backbonePaginator', 'helpers/common', 'models/user-model'], (Backbone, BackbonePaginator, Common, UserModel) ->
  console.log 'user collection loaded'

  Collection = Backbone.Paginator.requestPager.extend

    model: UserModel

    params: {}

    # url: ->
    #   window.baseUrl + 'apiv3/users?' + serialize(@params)

    paginator_core:
      # the type of the request (GET by default)
      type: 'GET'

      # the type of reply (jsonp by default)
      dataType: 'json'

      # the URL (or base URL) for the service
      url: ->
        window.baseUrl + 'apiv3/users?' + serialize(@params)

    paginator_ui:
      # the lowest page index your API allows to be accessed
      firstPage: 1

      # which page should the paginator start from
      # (also, the actual page the paginator is on)
      currentPage: 1

      # how many items per page should be shown
      perPage: 20

      # a default number of total pages to query in case the API or
      # service you are using does not support providing the total
      # number of pages for us.
      # 10 as a default in case your service doesn't return the total
      # totalPages: 10

      pagesInRange: 2

    server_api:
      # _.defaults(
        # the query field in the request
        'filter': ->
          @filter

        # number of items to return per request/page
        'limit': ->
          @perPage

        # how many results the request should skip ahead to
        # customize as needed. For the Netflix API, skipping ahead based on
        # page * number of results per page was necessary.
        'offset': ->
          (@currentPage - 1) * @perPage

        # field to sort by
        # 'orderby': 'ReleaseYear'

        # what format would you like to request results in?
        # 'format': 'json'

        # custom parameters
        # 'inlinecount': 'allpages'
        # 'callback': 'callback'
      # , @params)

    parse: (resp, xhr) ->
      @totalPages = resp.total_pages | 0
      @totalRecords = resp.total | 0

      if resp.success is true
        return resp.data;
      else if typeof resp.success isnt 'undefined'
        return @previousAttributes && @previousAttributes()

      return resp;
