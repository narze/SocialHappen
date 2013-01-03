(function() {

  define(['backbone', 'moment', 'text!templates/add-credits-modal-template.html'], function(Backbone, moment, AddCreditsModalTemplate) {
    var View;
    View = Backbone.View.extend({
      className: 'add-credits-modal-view',
      events: {
        'click .add-credits-save': 'addCredits'
      },
      initialize: function() {
        return _.bindAll(this);
      },
      render: function() {
        this.$el.html(_.template(AddCreditsModalTemplate, this.model.toJSON()));
        this.$('.modal').modal('show');
        $('#modal').html(this.el);
        return this;
      },
      addCredits: function() {
        var credits,
          _this = this;
        credits = this.$('.modal .credits-to-add').val();
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
              return _this.$('.modal').modal('hide');
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
