(function() {

  define(['backbone', 'text!templates/companies-template.html', 'views/companies-pagination-view', 'views/company-item-view'], function(Backbone, CompaniesTemplate, CompaniesPaginationView, CompanyItemView) {
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
        this.$el.html(CompaniesTemplate);
        this.delegateEvents();
        this.listCompanies();
        if (!this.subViews.pagination) {
          this.subViews.pagination = [];
          this.subViews.pagination[0] = new CompaniesPaginationView({
            collection: this.collection
          });
          this.subViews.pagination[1] = new CompaniesPaginationView({
            collection: this.collection
          });
        }
        this.$('.companies-pagination:first').html(this.subViews.pagination[0].render().el);
        this.$('.companies-pagination:last').html(this.subViews.pagination[1].render().el);
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
