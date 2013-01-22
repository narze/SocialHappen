define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/branch/edit-branch.html',
  'jqueryui',
  'events',
  'sandbox'
], function($, _, Backbone, editTemplate, jqueryui, vent, sandbox){
  var EditModalView = Backbone.View.extend({
    editTemplate: _.template(editTemplate),
    events: {
      'click h3.edit-title': 'showEditTitle',
      'click button.save-title': 'saveEditTitle',
      'click div.edit-address': 'showEditAddress',
      'click button.save-address': 'saveEditAddress',
      'click div.edit-hours': 'showEditHours',
      'click button.save-hours': 'saveEditHours',
      'click div.edit-telephone': 'showEditTelephone',
      'click button.save-telephone': 'saveEditTelephone',
      'click div.edit-location': 'showEditLocation',
      'click button.save-location': 'saveEditLocation',
      'click img.branch-photo, h6.edit-photo': 'showEditPhoto',
      'click button.save-photo': 'saveEditPhoto',
      'click button.upload-photo-submit': 'uploadPhoto',
      'click button.delete-branch': 'deleteBranch',
      'keyup input.google-maps-link': 'useGoogleMapsLink',
      'click button.view-google-maps': 'viewGoogleMaps'
    },

    initialize: function(){
      _.bindAll(this);
      vent.bind('showEditBranchModal', this.show);
    },

    render: function () {
      console.log('render modal');
      if(!this.model){
        return;
      }

      var data = this.model.toJSON();
      $(this.el).html(this.editTemplate(data));

      return this;
    },

    show: function(model){
      this.showEdit(model);
    },

    showEdit: function(model) {
      this.model = model;
      console.log('show edit modal:', model.toJSON());
      this.render();

      this.$el.modal('show');
    },

    showEditTitle: function() {
      $('h3.edit-title', this.el).hide();
      $('div.edit-title', this.el).show();
      $('input.branch-title', this.el).focus();
    },

    saveEditTitle: function() {

      var title = $('input.branch-title', this.el).val();

      this.model.set('title', title).trigger('change');
      this.model.save();

      $('h3.edit-title', this.$el).html(title).show();
      $('div.edit-title', this.$el).hide();

      console.log('save title', title);

      // vent.trigger('showEditBranchModal', this.model);
    },

    showEditAddress: function() {
      $('div.edit-address', this.el).hide();
      $('div.edit-address-field', this.el).show();
    },

    saveEditAddress: function() {

      var address = $('textarea.branch-address', this.el).val();

      this.model.set('address', address).trigger('change');
      this.model.save();

      $('div.edit-address p', this.el).html(address);
      $('div.edit-address', this.el).show();
      $('div.edit-address-field', this.el).hide();

      // vent.trigger('showEditBranchModal', this.model);
    },

    showEditHours: function() {
      $('div.edit-hours', this.el).hide();
      $('div.edit-hours-field', this.el).show();
    },

    saveEditHours: function() {

      var hours = $('textarea.branch-hours', this.el).val();

      this.model.set('hours', hours).trigger('change');
      this.model.save();

      $('div.edit-hours p', this.el).html(hours);
      $('div.edit-hours', this.el).show();
      $('div.edit-hours-field', this.el).hide();

      // vent.trigger('showEditBranchModal', this.model);
    },

    showEditTelephone: function() {
      $('div.edit-telephone', this.el).hide();
      $('div.edit-telephone-field', this.el).show();
    },

    saveEditTelephone: function() {

      var telephone = $('input.branch-telephone', this.el).val();

      this.model.set('telephone', telephone).trigger('change');
      this.model.save();

      $('div.edit-telephone p', this.el).html(telephone);
      $('div.edit-telephone', this.el).show();
      $('div.edit-telephone-field', this.el).hide();

      // vent.trigger('showEditBranchModal', this.model);
    },

    showEditLocation: function() {
      $('div.edit-location', this.el).hide();
      $('div.edit-location-field', this.el).show();
    },

    saveEditLocation: function() {

      var lat = parseFloat($('input.lat', this.el).val()) || 0;
      var lng = parseFloat($('input.lng', this.el).val()) || 0;

      this.model.set('location', { '0':lat, '1':lng }).trigger('change');
      this.model.save();

      $('div.edit-location p span.lat', this.el).text(lat);
      $('div.edit-location p span.lng', this.el).text(lng);
      $('div.edit-location', this.el).show();
      $('div.edit-location-field', this.el).hide();

      // vent.trigger('showEditBranchModal', this.model);
    },


    showEditPhoto: function() {
      console.log('show edit photo');
      $('div.edit-photo', this.el).show();
    },

    saveEditPhoto: function() {
      $('div.edit-photo', this.el).hide();

      var photo = $('input.branch-photo', this.el).val();

      this.model.set('photo', photo).trigger('change');
      this.model.save();
      // $('img.branch-photo').attr('src', photo)

      vent.trigger('showEditBranchModal', this.model);
    },

    uploadPhoto: function(e) {
      e.preventDefault();
      var self = this;
      $('form.upload-photo', this.el).ajaxSubmit({
        beforeSubmit: function(a,f,o) {
          o.dataType = 'json';
        },
        success: function(resp) {
          if(resp.success) {
            var photoUrl = resp.data;

            // Save photo
            self.model.set('photo', photoUrl).trigger('change');
            self.model.save();

            vent.trigger('showEditBranchModal', self.model);
            return;
          }
          alert(resp.data);
        }
      })
    },

    deleteBranch: function(e){
      e.preventDefault();
      var confirm = window.confirm('Are you sure you want to delete this branch ?');
      if(confirm){
        this.model.destroy();
        this.$el.modal('hide');
      }
    },

    useGoogleMapsLink: function(e) {
      e.preventDefault();

      var link = this.$('input.google-maps-link').val()
        , latlng = getParameterByName(link, 'q').split(',')
        , lat = latlng[0]
        , lng = latlng[1]

      if(lat && lng) {
        this.$('input.lat').val(lat)
        this.$('input.lng').val(lng)
      }

      function getParameterByName(string, name)
      {
        string = "?" + string.split('?')[1];
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regexS = "[\\?&]" + name + "=([^&#]*)";
        var regex = new RegExp(regexS);
        var results = regex.exec(string);
        if(results === null)
          return "";
        else
          return decodeURIComponent(results[1].replace(/\+/g, " "));
      }
    },

    viewGoogleMaps: function(e) {
      e.preventDefault();

      var self = this
        , marker = false
        , $formLatitude = this.$('input.lat')
        , $formLongitude = this.$('input.lng')

      require(['gmaps'], function(GMaps) {
        $('#gmaps').css({
          width: '100%',
          height: 300
        });

        var map = new GMaps({
          div: '#gmaps',
          lat: 0,
          lng: 0,
          zoom: 16,
          click: function(e) {
            console.log(e);
            $formLatitude.val(e.latLng.Ya)
            $formLongitude.val(e.latLng.Za)

            if(!!marker) {
              map.removeMarker(marker);
            }
            marker = map.addMarker({
              lat: e.latLng.Ya,
              lng: e.latLng.Za
            });

            map.refresh();
          }
        });

        if(!$formLatitude.val().length && !$formLongitude.val().length) {
          GMaps.geolocate({
            success: function(position) {
              map.setCenter(position.coords.latitude, position.coords.longitude);
            },
            error: function(error) {
              console.log('Geolocation failed: '+error.message);
            },
            not_supported: function() {
              console.log("Your browser does not support geolocation");
            },
            always: function() {
              console.log("Done!");
            }
          });
        } else {
          map.setCenter($formLatitude.val(), $formLongitude.val());
          marker = map.addMarker({
            lat: $formLatitude.val(),
            lng: $formLongitude.val()
          });
        }
      });
    }
  });
  return EditModalView;
});
