define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge'
], function($, _, Backbone, challengeModel){
  var challengesCollection = Backbone.Collection.extend({
    model: challengeModel,

    last_id: null,

    initialize: function(){
      _.bindAll(this);
    },

    loadMore: function(callback){
      if(this.models.length == 0){
        this.last_id = null;
      }else{
        this.last_id = this.last().id;
      }

      this.fetch({
        add: true,
        success: function(collection, resp){
          callback(resp.length);
        }
      });
    },

    sync: function(method, model, options) {
      var methodMap = {

        'create': 'POST',
        'update': 'PUT',
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
        if(this.last_id && window.Company.companyId){
          params.url = window.Company.BASE_URL + 'apiv3/challenges/?last_id=' + this.last_id + '&company_id=' + window.Company.companyId;
        }else if(window.Company.companyId){
          params.url = window.Company.BASE_URL + 'apiv3/challenges/?company_id=' + window.Company.companyId;
        }

      }

      // Ensure that we have the appropriate request data.
      if (!options.data && model && (method == 'create' || method == 'update')) {
        params.contentType = 'application/json';
        params.data = JSON.stringify(model.toJSON());
      }

      // For older servers, emulate JSON by encoding the request into an HTML-form.
      if (Backbone.emulateJSON) {
        params.contentType = 'application/x-www-form-urlencoded';
        params.data = params.data ? {model: params.data} : {};
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

      // Make the request, allowing the user to override any Ajax options.
      return $.ajax(_.extend(params, options));
    }

  });

  return challengesCollection;
});
