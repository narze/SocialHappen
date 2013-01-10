(function() {

  define(['backbone', 'text!templates/companies-template.html', 'views/companies-filter-view', 'views/pagination-view', 'views/company-item-view'], function(Backbone, CompaniesTemplate, CompaniesFilterView, PaginationView, CompanyItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'companies-view',
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listCompanies);
        this.collection.bind('change', this.listCompanies);
        return this.collection.fetch();
      },
      listCompanies: function() {
        this.$('#company-list').empty();
        return this.collection.each(function(model) {
          return this.addCompany(model);
        }, this);
      },
      addCompany: function(model) {
        var company;
        company = new CompanyItemView({
          model: model
        });
        this.subViews['company-' + model.cid] = company;
        return this.$('#company-list').append(company.render().el);
      },
      render: function() {
        var i, paginationCount, _i, _j;
        this.$el.html(CompaniesTemplate);
        this.delegateEvents();
        this.listCompanies();
        if (!this.subViews.filter) {
          this.subViews.filter = new CompaniesFilterView({
            collection: this.collection
          });
        }
        this.$('.companies-filter-container').html(this.subViews.filter.render().el);
        paginationCount = this.$('.pagination-container').length;
        if (paginationCount) {
          if (!this.subViews.pagination) {
            this.subViews.pagination = [];
            for (i = _i = 0; 0 <= paginationCount ? _i <= paginationCount : _i >= paginationCount; i = 0 <= paginationCount ? ++_i : --_i) {
              this.subViews.pagination[i] = new PaginationView({
                collection: this.collection
              });
            }
          }
          for (i = _j = 0; 0 <= paginationCount ? _j <= paginationCount : _j >= paginationCount; i = 0 <= paginationCount ? ++_j : --_j) {
            this.$('.pagination-container:eq(' + i + ')').html(this.subViews.pagination[i].render().el);
          }
        }
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
