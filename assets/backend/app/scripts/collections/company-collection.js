(function() {

  define(['backbone', 'backbonePaginator', 'helpers/common', 'models/company-model'], function(Backbone, BackbonePaginator, Common, CompanyModel) {
    var Collection;
    console.log('company collection loaded');
    return Collection = Backbone.Paginator.requestPager.extend({
      model: CompanyModel,
      params: {},
      paginator_core: {
        type: 'GET',
        dataType: 'json',
        url: function() {
          return window.baseUrl + 'apiv3/companies?' + serialize(this.params);
        }
      },
      paginator_ui: {
        firstPage: 1,
        currentPage: 1,
        perPage: 1,
        pagesInRange: 2
      },
      server_api: {
        'filter': '',
        'limit': function() {
          return this.perPage;
        },
        'offset': function() {
          return (this.currentPage - 1) * this.perPage;
        }
      },
      parse: function(resp, xhr) {
        this.totalPages = resp.total_pages | 0;
        this.totalRecords = resp.total | 0;
        if (resp.success === true) {
          return resp.data;
        } else if (typeof resp.success !== 'undefined') {
          return this.previousAttributes && this.previousAttributes();
        }
        return resp;
      }
    });
  });

}).call(this);
