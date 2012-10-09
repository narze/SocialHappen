define([
  'underscore',
  'backbone'
], function(_, Backbone) {
  var offerModel = Backbone.Model.extend({

    idAttribute: '_id',

    defaults: {
      name: '',
      image: null,
      value: 0,
      description: '',
      company_id: window.Company.companyId,
      status: 'published',
      type: 'offer',
      redeem_method: 'in_store'
    },
    initialize: function(){

    },

    sync: function(method, model, options) {
      var methodMap = {
        'create': 'POST',
        'update': 'POST',
        'delete': 'DELETE',
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
          params.url = window.Company.BASE_URL + 'apiv3/saveOffer/' + this.id;
        }else if(method == 'create'){
          params.url = window.Company.BASE_URL + 'apiv3/saveOffer/';
        }
      }

      // Ensure that we have the appropriate request data.
      if (!options.data && model && (method == 'create' || method == 'update')) {
        params.contentType = 'application/json';
        var data = model.toJSON();
        delete data.offerOffers;
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

      console.log('save offer:', this.toJSON());
      console.log('POST:', params.data);

      return $.ajax(_.extend(params, options));
    },

    //parse saveOffer's response
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
  return offerModel;

});
