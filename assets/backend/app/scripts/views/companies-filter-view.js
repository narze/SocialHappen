(function() {

  define(['backbone', 'text!templates/companies-filter-template.html', 'moment', 'jqueryPlugins/jquery.chosen.min'], function(Backbone, CompaniesFilterTemplate, mm, chosen) {
    var View;
    View = Backbone.View.extend({
      id: 'companies-filter-view',
      events: {
        'click .box-header': 'minimize',
        'submit form.companies-filter': 'filter'
      },
      initialize: function() {
        return _.bindAll(this);
      },
      minimize: function(e) {
        var $target;
        e.preventDefault();
        $target = this.$el.find('.box-content');
        if ($target.is(':visible')) {
          this.$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down');
        } else {
          $('[data-rel="chosen"],[rel="chosen"]').chosen();
          this.$('.box-header .btn-minimize i').removeClass('icon-chevron-down').addClass('icon-chevron-up');
        }
        return $target.slideToggle();
      },
      filter: function(e) {
        e.preventDefault();
        this.collection.filter = {
          name: this.$('#filter-name').val(),
          created_at_from: this.$('#filter-created-at-from').val() ? moment(this.$('#filter-created-at-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          created_at_to: this.$('#filter-created-at-to').val() ? moment(this.$('#filter-created-at-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          credits: this.$('#filter-credits').val()
        };
        return this.collection.fetch();
      },
      render: function() {
        this.$el.html(CompaniesFilterTemplate);
        this.delegateEvents();
        if (this.$('.datepicker')) {
          this.$('.datepicker').datepicker();
        }
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
