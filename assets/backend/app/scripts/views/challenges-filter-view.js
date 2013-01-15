(function() {

  define(['backbone', 'text!templates/challenges-filter-template.html', 'moment'], function(Backbone, ChallengesFilterTemplate, mm) {
    var View;
    View = Backbone.View.extend({
      id: 'challenges-filter-view',
      events: {
        'click .box-header': 'minimize',
        'submit form.challenges-filter': 'filter'
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
          name: this.$('#filter-name').val(),
          sonar_data: this.$('#filter-sonar-data').val(),
          start_date_from: this.$('#filter-start-date-from').val() ? moment(this.$('#filter-start-date-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          start_date_to: this.$('#filter-start-date-to').val() ? moment(this.$('#filter-start-date-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          end_date_from: this.$('#filter-end-date-from').val() ? moment(this.$('#filter-end-date-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          end_date_to: this.$('#filter-end-date-to').val() ? moment(this.$('#filter-end-date-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0
        };
        return this.collection.fetch();
      },
      render: function() {
        this.$el.html(ChallengesFilterTemplate);
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
