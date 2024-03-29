(function() {

  define(['backbone', 'text!templates/devices-filter-template.html', 'moment'], function(Backbone, DevicesFilterTemplate, mm) {
    var View;
    View = Backbone.View.extend({
      id: 'devices-filter-view',
      events: {
        'click .box-header': 'minimize',
        'submit form.devices-filter': 'filter'
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
          challenge: this.$('#filter-challenge').val(),
          data: this.$('#filter-data').val()
        };
        return this.collection.fetch();
      },
      render: function() {
        this.$el.html(DevicesFilterTemplate);
        this.delegateEvents();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
