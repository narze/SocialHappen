(function() {

  define(['backbone', 'backboneValidation'], function(Backbone, BackboneValidation) {
    var Model;
    console.log('device model loaded');
    return Model = Backbone.Model.extend({
      url: function() {
        return window.baseUrl + 'apiv3/devices';
      },
      defaults: {
        id: null,
        title: null,
        data: null,
        company: null,
        branch: null
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
        id: {
          required: true,
          msg: 'ID should not be blank'
        },
        title: {
          required: true,
          msg: 'Title should not be blank'
        },
        data: {
          required: true,
          msg: 'Data should not be blank'
        },
        company: {
          required: true,
          msg: 'Please choose a company'
        },
        branch: {
          required: true,
          msg: 'Please choose a branch'
        }
      }
    });
  });

}).call(this);
