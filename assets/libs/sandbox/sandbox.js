define(['underscore', 'backbone'], function(_, Backbone) {
  var sandbox = {}
  sandbox.events = _.extend({}, Backbone.Events)
  sandbox.models = {}
  sandbox.collections = {}
  return sandbox
})