(function() {

  define(['backbone', 'text!templates/companies-template.html', 'views/company-item-view'], function(Backbone, CompaniesTemplate, CompanyItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'companies-view',
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listCompanies);
        return this.collection.fetch();
      },
      listCompanies: function() {
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
        this.listCompanies();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
