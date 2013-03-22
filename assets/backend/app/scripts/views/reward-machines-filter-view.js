(function() {

  define(['backbone', 'text!templates/reward-machines-filter-template.html', 'moment'], function(Backbone, RewardMachinesFilterTemplate, mm) {
    var View;
    View = Backbone.View.extend({
      id: 'reward-machines-filter-view',
      events: {
        'click .box-header': 'minimize',
        'submit form.reward-machines-filter': 'filter'
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
          id: this.$('#filter-id').val(),
          name: this.$('#filter-name').val(),
          description: this.$('#filter-description').val()
        };
        return this.collection.fetch();
      },
      render: function() {
        this.$el.html(RewardMachinesFilterTemplate);
        this.delegateEvents();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
