(function() {

  define(['backbone', 'text!templates/rewards-filter-template.html', 'moment'], function(Backbone, RewardsFilterTemplate, mm) {
    var View;
    View = Backbone.View.extend({
      id: 'rewards-filter-view',
      events: {
        'click .box-header': 'minimize',
        'submit form.rewards-filter': 'filter'
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
          point_from: this.$('#filter-point-required-from').val(),
          point_to: this.$('#filter-point-required-to').val(),
          amount_from: this.$('#filter-amount-from').val(),
          amount_to: this.$('#filter-amount-to').val(),
          amount_redeemed_from: this.$('#filter-amount-redeemed-from').val(),
          amount_redeemed_to: this.$('#filter-amount-redeemed-to').val(),
          once: this.$('#filter-can-play-once').val()
        };
        return this.collection.fetch();
      },
      render: function() {
        this.$el.html(RewardsFilterTemplate);
        this.delegateEvents();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
