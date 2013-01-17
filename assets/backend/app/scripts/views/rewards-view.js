(function() {

  define(['backbone', 'text!templates/rewards-template.html', 'views/rewards-filter-view', 'views/pagination-view', 'views/reward-item-view'], function(Backbone, RewardsTemplate, RewardsFilterView, PaginationView, RewardItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'rewards-view',
      events: {
        'click .sort-name': 'sort',
        'click .sort-point-required': 'sort',
        'click .sort-amount': 'sort',
        'click .sort-amount-redeemed': 'sort',
        'click .sort-can-play-once': 'sort'
      },
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listRewards);
        this.collection.bind('change', this.listRewards);
        return this.collection.fetch();
      },
      listRewards: function() {
        this.$('#reward-list').empty();
        return this.collection.each(function(model) {
          return this.addReward(model);
        }, this);
      },
      addReward: function(model) {
        var reward;
        reward = new RewardItemView({
          model: model
        });
        this.subViews['reward-' + model.cid] = reward;
        return this.$('#reward-list').append(reward.render().el);
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
        } else if ($target.hasClass('sort-point-required')) {
          this.collection.sort = 'point';
        } else if ($target.hasClass('sort-amount')) {
          this.collection.sort = 'amount';
        } else if ($target.hasClass('sort-amount-redeemed')) {
          this.collection.sort = 'amount_redeemed';
        } else if ($target.hasClass('sort-can-play-once')) {
          this.collection.sort = 'once';
        }
        return this.collection.fetch();
      },
      render: function() {
        var i, paginationCount, _i, _j;
        this.$el.html(RewardsTemplate);
        this.delegateEvents();
        this.listRewards();
        if (!this.subViews.filter) {
          this.subViews.filter = new RewardsFilterView({
            collection: this.collection
          });
        }
        this.$('.rewards-filter-container').html(this.subViews.filter.render().el);
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
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
