(function() {

  define(['backbone', 'text!templates/reward-machines-template.html', 'views/reward-machines-filter-view', 'views/pagination-view', 'views/reward-machine-item-view', 'views/reward-machine-add-view'], function(Backbone, RewardMachinesTemplate, RewardMachinesFilterView, PaginationView, RewardMachineItemView, RewardMachineAddView) {
    var View;
    View = Backbone.View.extend({
      id: 'reward-machines-view',
      events: {
        'click .sort-name': 'sort',
        'click .sort-id': 'sort'
      },
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listRewardMachines);
        this.collection.bind('add', this.listRewardMachines);
        this.collection.bind('remove', this.listRewardMachines);
        return this.collection.fetch();
      },
      listRewardMachines: function() {
        this.$('#reward-machine-list').empty();
        return this.collection.each(function(model) {
          return this.addRewardMachine(model);
        }, this);
      },
      addRewardMachine: function(model) {
        var rewardMachine;
        rewardMachine = new RewardMachineItemView({
          model: model
        });
        this.subViews['reward-machine-' + model.cid] = rewardMachine;
        return this.$('#reward-machine-list').append(rewardMachine.render().el);
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
        } else if ($target.hasClass('sort-id')) {
          this.collection.sort = '_id';
        }
        return this.collection.fetch();
      },
      render: function() {
        var i, paginationCount, _i, _j;
        this.$el.html(RewardMachinesTemplate);
        this.delegateEvents();
        this.listRewardMachines();
        if (!this.subViews.filter) {
          this.subViews.filter = new RewardMachinesFilterView({
            collection: this.collection
          });
        }
        this.$('.reward-machines-filter-container').html(this.subViews.filter.render().el);
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
        if (!this.subViews['reward-machine-add']) {
          this.subViews['reward-machine-add'] = new RewardMachineAddView({
            model: new this.collection.model
          });
        }
        this.$('#reward-machine-add-container').html(this.subViews['reward-machine-add'].render().el);
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);