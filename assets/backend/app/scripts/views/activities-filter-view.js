(function() {

  define(['backbone', 'text!templates/activities-filter-template.html', 'moment'], function(Backbone, ActivitiesFilterTemplate, mm) {
    var View;
    View = Backbone.View.extend({
      id: 'activities-filter-view',
      events: {
        'click .box-header': 'minimize',
        'submit form.activities-filter': 'filter'
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
