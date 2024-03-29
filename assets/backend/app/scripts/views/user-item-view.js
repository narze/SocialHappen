(function() {

  define(['backbone', 'moment', 'text!templates/user-item-template.html'], function(Backbone, moment, UserItemTemplate) {
    var View;
    View = Backbone.View.extend({
      tagName: 'tr',
      className: 'user-item',
      initialize: function() {
        _.bindAll(this);
        return this.model.bind('change', this.render);
      },
      render: function() {
        var platforms, user;
        platforms = _.chain(this.model.get('user_platforms')).intersection(['ios', 'android']).map(function(platform) {
          if (platform === 'ios') {
            return 'iOS';
          }
          if (platform === 'android') {
            return 'Android';
          }
        }).value();
        user = _.clone(this.model.toJSON());
        user.user_platforms = platforms;
        this.$el.html(_.template(UserItemTemplate, user));
        return this;
      }
    });
    return View;
  });

}).call(this);
