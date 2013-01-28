(function() {

  define(['backbone', 'moment', 'text!templates/device-item-template.html'], function(Backbone, moment, DeviceItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'device-item',
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      "void": function(e) {
        return e.preventDefault();
      },
      render: function() {
        this.$el.html(_.template(DeviceItemTemplate, this.model.toJSON()));
        this.delegateEvents();
        return this;
      }
    });
    return View;
  });

}).call(this);