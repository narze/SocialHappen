(function() {

  define(['backbone', 'text!templates/companies-template.html', 'views/company-item-view'], function(Backbone, CompaniesTemplate, CompanyItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'companies-view',
      events: {
        'click a.servernext': 'nextResultPage',
        'click a.serverprevious': 'previousResultPage',
        'click a.serverlast': 'gotoLast',
        'click a.page': 'gotoPage',
        'click a.serverfirst': 'gotoFirst'
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
        this.collection.each(function(model) {
          return this.addCompany(model);
        }, this);
        return this.pagination();
      },
      addCompany: function(model) {
        var company;
        company = new CompanyItemView({
          model: model
        });
        this.subViews['company-' + model.cid] = company;
        return this.$('#company-list').append(company.render().el);
      },
      pagination: function() {
        return this.$('.companies-pagination').html(_.template(this.$('#companies-pagination-template').html(), this.collection.info()));
      },
      nextResultPage: function(e) {
        e.preventDefault();
        return this.collection.requestNextPage();
      },
      previousResultPage: function(e) {
        e.preventDefault();
        return this.collection.requestPreviousPage();
      },
      gotoFirst: function(e) {
        e.preventDefault();
        return this.collection.goTo(this.collection.information.firstPage);
      },
      gotoLast: function(e) {
        e.preventDefault();
        return this.collection.goTo(this.collection.information.lastPage);
      },
      gotoPage: function(e) {
        var page;
        e.preventDefault();
        page = $(e.target).text();
        return this.collection.goTo(page);
      },
      render: function() {
        this.$el.html(CompaniesTemplate);
        this.listCompanies();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
