(function() {

  define(['backbone', 'text!templates/companies-pagination-template.html'], function(Backbone, CompaniesPaginationTemplate) {
    var View;
    View = Backbone.View.extend({
      "class": 'companies-pagination-view',
      events: {
        'click a.servernext': 'nextResultPage',
        'click a.serverprevious': 'previousResultPage',
        'click a.serverlast': 'gotoLast',
        'click a.page': 'gotoPage',
        'click a.serverfirst': 'gotoFirst'
      },
      template: _.template(CompaniesPaginationTemplate),
      initialize: function() {
        _.bindAll(this);
        this.collection.bind('reset', this.render);
        return this.collection.bind('change', this.render);
      },
      pagination: function() {
        return this.$el.html(this.template(this.collection.info()));
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
        this.$el.html(this.template(this.collection.info()));
        this.delegateEvents();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
