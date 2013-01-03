(function() {

  define(['backbone', 'moment', 'text!templates/company-item-template.html', 'text!templates/add-credits-modal.html'], function(Backbone, moment, CompanyItemTemplate, AddCreditsModalTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'company-item',
      events: {
        'click .add-credits': 'addCreditsModal',
        'click .add-credits-save': 'addCredits'
      },
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      render: function() {
        this.$el.html(_.template(CompanyItemTemplate, this.model.toJSON()));
        return this;
      },
      addCreditsModal: function() {
        var modal;
        console.log('addCreditsModal');
        modal = this.$('.add-credits-modal');
        modal.html(_.template(AddCreditsModalTemplate, this.model.toJSON()));
        return modal.modal();
      },
      addCredits: function() {
        var credits,
          _this = this;
        credits = this.$('.add-credits-modal .credits-to-add').val();
        return $.ajax({
          dataType: 'json',
          type: 'post',
          data: {
            credit: credits,
            company_id: this.model.id
          },
          url: window.baseUrl + 'apiv3/credit_add',
          success: function(resp) {
            if (resp.success) {
              _this.updateCredits(resp.data.credits);
              return _this.$('.add-credits-modal').modal('hide');
            } else {
              return alert(resp.data);
            }
          }
        });
      },
      updateCredits: function(credits) {
        return this.model.set('credits', credits);
      }
    });
    return View;
  });

}).call(this);
