define([
  'underscore',
  'backbone'
], function(_, Backbone) {
  var challengeModel = Backbone.Model.extend({

    idAttribute: 'hash',

    defaults: {
      detail: {
        name: 'Challenge Name',
        description: 'Challenge Description',
        image: 'https://lh6.googleusercontent.com/JomHaEUw0LXXx3C7iggcx5R42Uu7KB7F9lHXrQqWW16ZGcTjpTs4P2RzKUvwiTAuBXYf4sEHiU8=s640-h400-e365'
      },
      hash: null,
      criteria: [],
      active: false,
      company_id: 0,
      reward: {
        name: 'Reward Name'
      },
      score: 10
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
          params.url = window.Company.BASE_URL + 'apiv3/saveChallenge/' + this.id
        }else if(method == 'create'){
          params.url = window.Company.BASE_URL + 'apiv3/saveChallenge/'
        }
      }

      // Ensure that we have the appropriate request data.
      if (!options.data && model && (method == 'create' || method == 'update')) {
        params.contentType = 'application/json';
        params.data = 'model='+JSON.stringify(model.toJSON());
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
      
      console.log('save challenge:', this.toJSON());
      console.log('POST:', params.data);
      
      // Make the request, allowing the user to override any Ajax options.
      return $.ajax(_.extend(params, options));
    }

  });
  return challengeModel;

});
