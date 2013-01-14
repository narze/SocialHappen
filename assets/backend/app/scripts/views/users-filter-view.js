(function() {

  define(['backbone', 'text!templates/users-filter-template.html', 'moment', 'jqueryPlugins/jquery.chosen.min'], function(Backbone, UsersFilterTemplate, mm, chosen) {
    var View;
    View = Backbone.View.extend({
      id: 'users-filter-view',
      events: {
        'click .box-header': 'minimize',
        'submit form.users-filter': 'filter',
        'reset form.users-filter': 'reset'
      },
      initialize: function() {
        return _.bindAll(this);
      },
      reset: function() {
        return this.$('#filter-platforms').next().find('li.search-choice .search-choice-close').click();
      },
      minimize: function(e) {
        var $target;
        e.preventDefault();
        $target = this.$el.find('.box-content');
        if ($target.is(':visible')) {
          this.$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down');
        } else {
          this.$('[data-rel="chosen"],[rel="chosen"]').chosen();
          this.$('.box-header .btn-minimize i').removeClass('icon-chevron-down').addClass('icon-chevron-up');
        }
        return $target.slideToggle();
      },
      filter: function(e) {
        e.preventDefault();
        this.collection.filter = {
          first_name: this.$('#filter-first-name').val(),
          last_name: this.$('#filter-last-name').val(),
          signup_date_from: this.$('#filter-signup-date-from').val() ? moment(this.$('#filter-signup-date-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          signup_date_to: this.$('#filter-signup-date-to').val() ? moment(this.$('#filter-signup-date-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          last_seen_from: this.$('#filter-last-seen-from').val() ? moment(this.$('#filter-last-seen-from').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          last_seen_to: this.$('#filter-last-seen-to').val() ? moment(this.$('#filter-last-seen-to').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          points: this.$('#filter-points').val(),
          platforms: this.$('#filter-platforms').val()
        };
        return this.collection.fetch();
      },
      render: function() {
        this.$el.html(UsersFilterTemplate);
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
