(function() {

  define(['backbone', 'text!templates/device-add-template.html', 'backboneValidationBootstrap', 'moment', 'jqueryPlugins/jquery.chosen.min'], function(Backbone, DeviceAddTemplate, BackboneValidationBootstrap, mm, jqform, chosen) {
    var View;
    View = Backbone.View.extend({
      id: 'device-add-view',
      events: {
        'submit form.device-add-form': 'addNewDevice',
        'click .box-header': 'minimize',
        'change #device-add-company': 'fetchBranches'
      },
      initialize: function() {
        _.bindAll(this);
        this.model = new window.backend.Models.DeviceModel;
        Backbone.Validation.bind(this);
        this.companyCollection = _.clone(window.backend.Collections.CompanyCollection);
        this.companyCollection.bind('reset', this.listCompanies);
        this.companyCollection.fetch();
        this.branchCollection = _.clone(window.backend.Collections.BranchCollection);
        return this.branchCollection.bind('reset', this.listBranches);
      },
      minimize: function(e) {
        var $target;
        e.preventDefault();
        $target = this.$el.find('.box-content');
        if ($target.is(':visible')) {
          this.$('.box-header .btn-minimize i').removeClass('icon-chevron-up').addClass('icon-chevron-down');
        } else {
          this.listCompanies();
          this.$('[data-rel="chosen"],[rel="chosen"]').chosen();
          this.$('.box-header .btn-minimize i').removeClass('icon-chevron-down').addClass('icon-chevron-up');
        }
        $target.slideToggle();
        return this.$("form :input:visible:enabled:first").focus();
      },
      addNewDevice: function(e) {
        var newDevice,
          _this = this;
        e.preventDefault();
        newDevice = {
          id: this.$('#device-add-id').val(),
          title: this.$('#device-add-title').val(),
          data: this.$('#device-add-data').val(),
          company: this.$('#device-add-company').val(),
          branch: this.$('#device-add-branch').val(),
          status: 'pending',
          created_at: (new Date()).getTime() / 1000 << 0,
          installed_at: null,
          info: {}
        };
        console.log(newDevice);
        if (this.model.set(newDevice)) {
          return this.model.save(null, {
            success: function() {
              window.backend.Collections.DeviceCollection.add(_this.model.clone());
              return _this.render();
            }
          });
        }
      },
      listCompanies: function() {
        var _this = this;
        this.$('#device-add-company').html('<option value="">Select Company</option>');
        this.companyCollection.each(function(model) {
          return _this.$('#device-add-company').append('<option value="' + model.get('company_id') + '">' + model.get('company_name') + '</option>');
        });
        return this.$('#device-add-company').trigger("liszt:updated");
      },
      fetchBranches: function() {
        var companyId;
        companyId = this.$('#device-add-company').val();
        this.branchCollection.filter = {
          company_id: companyId
        };
        return this.branchCollection.fetch();
      },
      listBranches: function() {
        var _this = this;
        this.$('#device-add-branch').html('<option value="">Select Branch</option>');
        if (!this.branchCollection.models.length) {
          this.$('#device-add-branch').html('<option value="">No Branch</option>');
        }
        this.branchCollection.each(function(model) {
          console.log(model.attributes);
          return _this.$('#device-add-branch').append('<option value="' + model.id + '">' + model.get('title') + '</option>');
        });
        return this.$('#device-add-branch').trigger("liszt:updated");
      },
      render: function() {
        this.$el.html(_.template(DeviceAddTemplate, {}));
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