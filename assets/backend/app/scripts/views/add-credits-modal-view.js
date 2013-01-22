(function() {

  define(['backbone', 'moment', 'text!templates/add-credits-modal-template.html'], function(Backbone, moment, AddCreditsModalTemplate) {
    var View;
    View = Backbone.View.extend({
      className: 'add-credits-modal-view',
      events: {
        'click .cancel': 'cancel',
        'click .add-credits-confirm': 'addCredits'
      },
      initialize: function() {
        return _.bindAll(this);
      },
      render: function() {
        var _this = this;
        this.$el.html(_.template(AddCreditsModalTemplate, this.model.toJSON()));
        this.$('.modal').modal('show');
        $('#modal').html(this.el);
        this.$('.add-credits-save').popover({
          html: true,
          content: function() {
            _this.credits = _this.$(".modal .credits-to-add").val();
            return ['<p>', _this.credits + ' credits will be added into ' + _this.model.get('company_name'), '</p>', '<p>', '<button class="btn cancel">Cancel</button>', '<button class="btn btn-primary add-credits-confirm" data-dismiss="modal">Confirm</button>'];
          }
        });
        return this;
      },
      cancel: function() {
        return this.$('.add-credits-save').popover('hide');
      },
      addCredits: function() {
        var _this = this;
        return $.ajax({
          dataType: 'json',
          type: 'post',
          data: {
            credit: this.credits,
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
