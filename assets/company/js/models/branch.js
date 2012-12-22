define([
  'underscore',
  'backbone'
], function(_, Backbone) {
  var BranchModel = Backbone.Model.extend({

    idAttribute: '_id',

    defaults: {
      title: '',
      photo: null,
      telephone: null,
      address: '',
      company_id: window.Company.companyId,
      location: [50,50]
    },

    initialize: function(){

    },

    sync: function(method, model, options) {
      var methodMap = {
        'create': 'POST',
        'update': 'POST',
        'delete': 'POST',
        'read':   'GET'
      };

      var type = methodMap[method];

      // Default options, unless specified.
      options || (options = {});

      // Default JSON-request options.
      var params = {type: type, dataType: 'json'};

      // Ensure that we have a URL.
      if (!options.url) {
        if(method == 'update'){
          params.url = window.Company.BASE_URL + 'apiv3/saveBranch/' + this.id;
        }else if(method == 'create'){
          params.url = window.Company.BASE_URL + 'apiv3/saveBranch/';
        }else if(method == 'delete'){
          params.url = window.Company.BASE_URL + 'apiv3/deleteBranch/' + this.id;
        }
      }

      // Ensure that we have the appropriate request data.
      if (!options.data && model && (method == 'create' || method == 'update' || method == 'delete')) {
        params.contentType = 'application/json';
        var data = model.toJSON();
        params.data = 'model='+encodeURIComponent(JSON.stringify(data));
      }

      // For older servers, emulate JSON by encoding the request into an HTML-form.
      if (Backbone.emulateJSON || true) {
        params.contentType = 'application/x-www-form-urlencoded';
        // params.data = params.data ? {model: params.data} : {};
      }

      // For older servers, emulate HTTP by mimicking the HTTP method with `_method`
      // And an `X-HTTP-Method-Override` header.
      if (Backbone.emulateHTTP) {
        if (type === 'PUT' || type === 'DELETE') {
          if (Backbone.emulateJSON) params.data._method = type;
          params.type = 'POST';
          params.beforeSend = function(xhr) {
            xhr.setRequestHeader('X-HTTP-Method-Override', type);
          };
        }
      }

      // Don't process data on a non-GET request.
      if (params.type !== 'GET' && !Backbone.emulateJSON) {
        params.processData = false;
      }

      console.log('save branch:', this.toJSON());
      console.log('POST:', params.data);

      return $.ajax(_.extend(params, options));
    },

    //parse saveReward's response
    //{success: [success?], 'data': [data]}
    parse: function(resp, xhr) {
      if(resp.success === true) {
        return resp.data;
      } else if(typeof resp.success !== 'undefined') {
        return this.previousAttributes();
      }

      //if resp.success is undefined, resp itself is data
      return resp;
    }

  });
  return BranchModel;

});
