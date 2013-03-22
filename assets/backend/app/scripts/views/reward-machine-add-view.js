(function() {

  define(['backbone', 'text!templates/reward-machine-add-template.html', 'backboneValidationBootstrap', 'moment'], function(Backbone, RewardMachineAddTemplate, BackboneValidationBootstrap, mm, jqform, chosen) {
    var View;
    View = Backbone.View.extend({
      id: 'reward-machine-add-view',
      events: {
        'submit form.reward-machine-add-form': 'addNewRewardMachine',
        'click .box-header': 'minimize'
      },
      initialize: function() {
        _.bindAll(this);
        this.model = new window.backend.Models.RewardMachineModel;
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
      addNewRewardMachine: function(e) {
        var newRewardMachine,
          _this = this;
        e.preventDefault();
        newRewardMachine = {
          name: this.$('#reward-machine-add-name').val(),
          description: this.$('#reward-machine-add-description').val(),
          location: [this.$('#reward-machine-add-location-longitude').val(), this.$('#reward-machine-add-location-latitude').val()]
        };
        console.log(newRewardMachine);
        if (this.model.set(newRewardMachine)) {
          return this.model.save(null, {
            success: function() {
              window.backend.Collections.RewardMachineCollection.add(_this.model.clone());
              return _this.render();
            }
          });
        }
      },
      render: function() {
        this.$el.html(_.template(RewardMachineAddTemplate, {}));
        this.delegateEvents();
        this.rendered = true;
        return this;
      }
    });
    return View;
  });

}).call(this);
