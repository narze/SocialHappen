define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/push-notifications.html',
  'events',
  'sandbox'
], function($, _, Backbone, pushNotificationsTemplate, vent, sandbox){
  var CompanySettingsView = Backbone.View.extend({

    events: {
      'click button.add-push-notification': 'addPushNotification',
      'click button.push-to-device': 'pushToDevice'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      $(this.el).html(_.template(pushNotificationsTemplate)({}))
      return this;
    },

    clean: function() {
      this.remove();
      this.unbind();
    },

    synced: function() {
      $('.flash-message', this.el).html($('#pushed-template').html());
    },

    addPushNotification: function(e) {
      e.preventDefault()

      var message = $('#push-notification-message').val()
      if(!message.length) {
        alert('Please specify push push notification message')
        return;
      }

      $.ajax({
        url: window.Company.BASE_URL + 'apiv3/add_push_notification',
        data: {
          message: message
        },
        type: 'POST',
        dataType: 'json',
        success: function(resp) {
          if(resp.success) {

          }

          alert(resp.data)
          $('#push-notification-message').val('')
        }
      })
    },

    pushToDevice: function(e) {
      e.preventDefault()

      $.ajax({
        url: window.Company.BASE_URL + 'apiv3/push_notification_to_device',
        data: {},
        type: 'POST',
        dataType: 'json',
        success: function(resp) {
          if(resp.success) {

          }

          alert(resp.data)
        }
      })
    }

  });
  return CompanySettingsView;
});
