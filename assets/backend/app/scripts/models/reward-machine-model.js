(function() {

  define(['backbone'], function(Backbone) {
    var Model;
    console.log('reward machine model loaded');
    return Model = Backbone.Model.extend({
      url: function() {
        return window.baseUrl + 'apiv3/reward_machines';
      },
      defaults: {
        name: null,
        description: null,
        location: []
      },
      parse: function(resp, xhr) {
        if (resp.success === true) {
          this.set(resp.data);
          return resp.data;
        } else if (resp.success === false) {
          if (this.isNew()) {
            alert(resp.data);
            return this.collection.remove(this);
          }
          return this.previousAttributes && this.previousAttributes();
        }
        return resp;
      },
      validation: {
        name: {
          required: true,
          msg: 'Name should not be blank'
        }
      }
    });
  });

}).call(this);
