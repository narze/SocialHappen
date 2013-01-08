(function() {

  define(['backbone', 'backbonePaginator', 'helpers/common', 'models/user-model'], function(Backbone, BackbonePaginator, Common, UserModel) {
    var Collection;
    console.log('user collection loaded');
    return Collection = Backbone.Paginator.requestPager.extend({
      model: UserModel,
      params: {},
      paginator_core: {
        type: 'GET',
        dataType: 'json',
        url: function() {
          return window.baseUrl + 'apiv3/users?' + serialize(this.params);
        }
      },
      paginator_ui: {
        firstPage: 1,
        currentPage: 1,
        perPage: 20,
        pagesInRange: 2
      },
      server_api: {
        'filter': function() {
          return this.filter;
        },
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
