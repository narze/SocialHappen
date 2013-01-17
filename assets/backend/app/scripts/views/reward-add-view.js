(function() {

  define(['backbone', 'text!templates/reward-add-template.html', 'backboneValidationBootstrap', 'moment'], function(Backbone, RewardAddTemplate, BackboneValidationBootstrap, mm) {
    var View;
    View = Backbone.View.extend({
      id: 'reward-add-view',
      events: {
        'submit form.reward-add-form': 'addNewReward',
        'click .box-header': 'minimize'
      },
      initialize: function() {
        _.bindAll(this);
        this.model = new window.backend.Models.RewardModel;
        return Backbone.Validation.bind(this);
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
        $target.slideToggle();
        return this.$("form :input:visible:enabled:first").focus();
      },
      addNewReward: function(e) {
        var newReward,
          _this = this;
        e.preventDefault();
        newReward = {
          name: this.$('#reward-add-name').val(),
          description: this.$('#reward-add-description').val(),
          status: this.$('#reward-add-status').val(),
          redeem_method: this.$('#reward-add-redeem-method').val(),
          start_timestamp: this.$('#reward-add-start-timestamp').val() ? moment(this.$('#reward-add-start-timestamp').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          end_timestamp: this.$('#reward-add-end-timestamp').val() ? moment(this.$('#reward-add-end-timestamp').val(), "MM/DD/YYYY").format("YYYY/MM/DD") : void 0,
          redeem: {
            amount: this.$('#reward-add-amount').val(),
            point: this.$('#reward-add-point').val(),
            once: this.$('#reward-add-once').val(),
            amount_redeemed: 0
          },
          company_id: 1,
          image: '',
          is_points_reward: false,
          type: 'redeem'
        };
        if (this.model.set(newReward)) {
          return this.model.save(null, {
            success: function() {
              window.backend.Collections.RewardCollection.add(_this.model.clone());
              return _this.render();
            }
          });
        }
      },
      render: function() {
        this.$el.html(RewardAddTemplate);
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
