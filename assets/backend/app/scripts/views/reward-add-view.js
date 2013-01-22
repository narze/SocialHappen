(function() {

  define(['backbone', 'text!templates/reward-add-template.html', 'backboneValidationBootstrap', 'moment', 'jqueryForm'], function(Backbone, RewardAddTemplate, BackboneValidationBootstrap, mm, jqform) {
    var View;
    View = Backbone.View.extend({
      id: 'reward-add-view',
      events: {
        'submit form.reward-add-form': 'addNewReward',
        'click .box-header': 'minimize',
        'click a.upload-image': 'uploadImage'
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
          image: this.$('#reward-add-image').val(),
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
      uploadImage: function(e) {
        var _this = this;
        e.preventDefault();
        this.$('.reward-add-form .reward-add-image:first').after(this.$('.reward-add-form .reward-add-image:first').clone());
        this.$('form.upload-image .file-input').html(this.$('.reward-add-form .reward-add-image:first'));
        return this.$('form.upload-image').ajaxSubmit({
          beforeSubmit: function(a, f, o) {
            return o.dataType = 'json';
          },
          success: function(resp) {
            var imageUrl;
            if (resp.success) {
              imageUrl = resp.data;
              return _this.$('#reward-add-image').val(imageUrl);
            } else {
              return alert('There is an error uploading the image');
            }
          }
        });
      },
      render: function() {
        this.$el.html(_.template(RewardAddTemplate, {}));
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
