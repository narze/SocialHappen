(function() {

  define(['backbone', 'text!templates/companies-template.html', 'views/companies-filter-view', 'views/pagination-view', 'views/company-item-view'], function(Backbone, CompaniesTemplate, CompaniesFilterView, PaginationView, CompanyItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'companies-view',
      events: {
        'click .sort-name': 'sort',
        'click .sort-created-at': 'sort',
        'click .sort-credits': 'sort'
      },
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
      sort: function(e) {
        var $target;
        e.preventDefault();
        $target = $(e.currentTarget);
        if ($target.hasClass('sort-asc')) {
          $target.removeClass('sort-asc');
          $target.addClass('sort-desc');
          $target.removeClass('icon-chevron-up').addClass('icon-chevron-down');
          this.collection.order = '-';
        } else {
          $target.removeClass('sort-desc');
          $target.addClass('sort-asc');
          $target.removeClass('icon-chevron-down').addClass('icon-chevron-up');
          this.collection.order = '+';
        }
        if ($target.hasClass('sort-name')) {
          this.collection.sort = 'company_name';
        } else if ($target.hasClass('sort-created-at')) {
          this.collection.sort = 'company_register_date';
        } else if ($target.hasClass('sort-credits')) {
          this.collection.sort = 'credits';
        }
        return this.collection.fetch();
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
