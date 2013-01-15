(function() {

  define(['backbone', 'text!templates/challenges-template.html', 'views/challenges-filter-view', 'views/pagination-view', 'views/challenge-item-view'], function(Backbone, ChallengesTemplate, ChallengesFilterView, PaginationView, ChallengeItemView) {
    var View;
    View = Backbone.View.extend({
      id: 'challenges-view',
      events: {
        'click .sort-name': 'sort',
        'click .sort-start-date': 'sort',
        'click .sort-end-date': 'sort',
        'click .sort-sonar-data': 'sort'
      },
      initialize: function() {
        _.bindAll(this);
        this.subViews = {};
        this.collection.bind('reset', this.listChallenges);
        this.collection.bind('change', this.listChallenges);
        return this.collection.fetch();
      },
      listChallenges: function() {
        this.$('#challenge-list').empty();
        return this.collection.each(function(model) {
          return this.addChallenge(model);
        }, this);
      },
      addChallenge: function(model) {
        var challenge;
        challenge = new ChallengeItemView({
          model: model
        });
        this.subViews['challenge-' + model.cid] = challenge;
        return this.$('#challenge-list').append(challenge.render().el);
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
        } else if ($target.hasClass('sort-start-date')) {
          this.collection.sort = 'start_date';
        } else if ($target.hasClass('sort-end-date')) {
          this.collection.sort = 'end_date';
        } else if ($target.hasClass('sort-sonar-data')) {
          this.collection.sort = 'sonar_data';
        }
        return this.collection.fetch();
      },
      render: function() {
        var i, paginationCount, _i, _j;
        this.$el.html(ChallengesTemplate);
        this.delegateEvents();
        this.listChallenges();
        if (!this.subViews.filter) {
          this.subViews.filter = new ChallengesFilterView({
            collection: this.collection
          });
        }
        this.$('.challenges-filter-container').html(this.subViews.filter.render().el);
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
