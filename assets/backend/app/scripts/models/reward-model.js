(function() {

  define(['backbone', 'backboneValidation'], function(Backbone, BackboneValidation) {
    var Model;
    console.log('reward model loaded');
    return Model = Backbone.Model.extend({
      url: function() {
        return window.baseUrl + 'apiv3/reward_list';
      },
      defaults: {
        name: null,
        description: null,
        status: 'published',
        redeem: {
          point: 0,
          amount: 0,
          amount_redeemed: 0,
          once: true
        }
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
        },
        'redeem.amount': [
          {
            required: true,
            msg: 'Quantity should not be blank'
          }, {
            min: 1,
            msg: 'Quantity should be more than 0'
          }
        ],
        'redeem.point': [
          {
            required: true,
            msg: 'Point should not be blank'
          }, {
            min: 1,
            msg: 'Point should be more than 0'
          }
        ]
      }
    });
  });

}).call(this);
