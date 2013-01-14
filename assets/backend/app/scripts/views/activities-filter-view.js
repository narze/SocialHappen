(function() {

  define(['backbone', 'text!templates/activities-filter-template.html', 'collections/audit-action-collection', 'moment', 'jqueryPlugins/jquery.chosen.min'], function(Backbone, ActivitiesFilterTemplate, AuditActionCollection, mm, chosen) {
    var View;
    View = Backbone.View.extend({
      id: 'activities-filter-view',
      events: {
        'click .box-header': 'minimize',
        'submit form.activities-filter': 'filter',
        'reset form.activities-filter': 'reset'
      },
      initialize: function() {
        _.bindAll(this);
        this.auditActionCollection = new AuditActionCollection();
        this.auditActionCollection.bind('reset', this.prepareCollection);
        return this.auditActionCollection.fetch();
      },
      reset: function() {
        return this.$('#filter-action').next().find('li.search-choice .search-choice-close').click();
      },
      prepareCollection: function() {
        var _this = this;
        this.$('#filter-action').empty();
        this.auditActionCollection.each(function(model) {
          return _this.$('#filter-action').append('<option>' + model.get('description') + '</option>');
        });
        return this.$('#filter-action').trigger("liszt:updated");
      },
      minimize: function(e) {
        var $target;
        e.preventDefault();
        $target = this.$el.find('.box-content');
        if ($target.is(':visible')) {
          this.$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down');
        } else {
          this.$('[data-rel="chosen"],[rel="chosen"]').chosen();
          this.$('#filter-action').next().css({
            width: '220px'
          });
          this.$('.box-header .btn-minimize i').removeClass('icon-chevron-down').addClass('icon-chevron-up');
        }
        return $target.slideToggle();
      },
      filter: function(e) {
        e.preventDefault();
        this.collection.filter = {
          first_name: this.$('#filter-first-name').val(),
          last_name: this.$('#filter-last-name').val(),
          action: this.$('#filter-action').val(),
          date_from: this.$('#filter-date-from').val() ? moment(this.$('#filter-date-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          date_to: this.$('#filter-date-to').val() ? moment(this.$('#filter-date-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          company: this.$('#filter-company').val(),
          branch: this.$('#filter-branch').val(),
          challenge: this.$('#filter-challenge').val()
        };
        return this.collection.fetch();
      },
      render: function() {
        this.$el.html(ActivitiesFilterTemplate);
        this.prepareCollection();
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
