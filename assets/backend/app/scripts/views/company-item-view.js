(function() {

  define(['backbone', 'moment', 'text!templates/company-item-template.html', 'views/add-credits-modal-view'], function(Backbone, moment, CompanyItemTemplate, AddCreditsModalView) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'company-item',
      events: {
        'click .add-credits': 'showAddCreditsModal'
      },
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      render: function() {
        this.$el.html(_.template(CompanyItemTemplate, this.model.toJSON()));
        return this;
      },
      showAddCreditsModal: function() {
        var addCreditsModalView;
        console.log('showAddCreditsModal');
        addCreditsModalView = new AddCreditsModalView({
          model: this.model
        });
        return addCreditsModalView.render();
      }
    });
    return View;
  });

}).call(this);
