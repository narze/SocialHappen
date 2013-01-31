(function() {

  define(['backbone', 'text!templates/devices-template.html', 'views/devices-filter-view', 'views/pagination-view', 'views/device-item-view', 'views/device-add-view'], function(Backbone, DevicesTemplate, DevicesFilterView, PaginationView, DeviceItemView, DeviceAddView) {
    var View;
    View = Backbone.View.extend({
      id: 'devices-view',
      events: {
        'click .sort-name': 'sort',
        'click .sort-data': 'sort'
      },
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listDevices);
        this.collection.bind('add', this.listDevices);
        this.collection.bind('remove', this.listDevices);
        return this.collection.fetch();
      },
      listDevices: function() {
        this.$('#device-list').empty();
        return this.collection.each(function(model) {
          return this.addDevice(model);
        }, this);
      },
      addDevice: function(model) {
        var device;
        device = new DeviceItemView({
          model: model
        });
        this.subViews['device-' + model.cid] = device;
        return this.$('#device-list').append(device.render().el);
      },
      sort: function(e) {
        var $target;
        e.preventDefault();
        $target = $(e.currentTarget);
        if ($target.hasClass('sort-asc')) {
          $target.removeClass('sort-asc');
          $target.addClass('sort-desc');
          $target.removeClass('icon-chevron-up').addClass('icon-chevron-down');
          this.collection.order = '-';
        } else {
          $target.removeClass('sort-desc');
          $target.addClass('sort-asc');
          $target.removeClass('icon-chevron-down').addClass('icon-chevron-up');
          this.collection.order = '+';
        }
        if ($target.hasClass('sort-name')) {
          this.collection.sort = 'name';
        } else if ($target.hasClass('sort-data')) {
          this.collection.sort = 'data';
        }
        return this.collection.fetch();
      },
      render: function() {
        var i, paginationCount, _i, _j;
        this.$el.html(DevicesTemplate);
        this.delegateEvents();
        this.listDevices();
        if (!this.subViews.filter) {
          this.subViews.filter = new DevicesFilterView({
            collection: this.collection
          });
        }
        this.$('.devices-filter-container').html(this.subViews.filter.render().el);
        paginationCount = this.$('.pagination-container').length;
        if (paginationCount) {
          if (!this.subViews.pagination) {
            this.subViews.pagination = [];
            for (i = _i = 0; 0 <= paginationCount ? _i <= paginationCount : _i >= paginationCount; i = 0 <= paginationCount ? ++_i : --_i) {
              this.subViews.pagination[i] = new PaginationView({
                collection: this.collection
              });
            }
          }
          for (i = _j = 0; 0 <= paginationCount ? _j <= paginationCount : _j >= paginationCount; i = 0 <= paginationCount ? ++_j : --_j) {
            this.$('.pagination-container:eq(' + i + ')').html(this.subViews.pagination[i].render().el);
          }
        }
        if (!this.subViews['device-add']) {
          this.subViews['device-add'] = new DeviceAddView({
            model: new this.collection.model
          });
        }
        this.$('#device-add-container').html(this.subViews['device-add'].render().el);
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
