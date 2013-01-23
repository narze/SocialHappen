define([
  'jquery',
  'underscore',
  'backbone',
  'models/challenge',
  'text!templates/company/modal/branch/add-branch.html',
  'jqueryui',
  'events',
  'sandbox'
], function($, _, Backbone, ChallengeModel, addTemplate,
   jqueryui, vent, sandbox){
  var EditModalView = Backbone.View.extend({
    addTemplate: _.template(addTemplate),

    events: {
      'keyup input.branch-title': 'saveEditTitle',
      'keyup textarea.branch-address': 'saveEditAddress',
      'keyup textarea.branch-hours': 'saveEditHours',
      'keyup input.branch-telephone': 'saveEditTelephone',
      'keyup input.lat, input.lng': 'saveEditLocation',
      'click img.branch-photo, h6.edit-photo': 'showEditPhoto',
      'click button.save-photo': 'saveEditPhoto',
      'click button.create-branch': 'createBranch',
      'click button.upload-photo-submit': 'uploadPhoto',
      'keyup input.google-maps-link': 'useGoogleMapsLink',
      'click button.view-google-maps': 'viewGoogleMaps'
    },

    initialize: function() {
      _.bindAll(this);
      vent.bind('showAddBranchModal', this.show);
    },

    render: function () {
      console.log('render modal');

      if(!this.model) {
        return;
      }

      var data = this.model.toJSON();
      console.log(data);
      $(this.el).html(this.addTemplate(data));

      return this;
    },

    show: function(model) {
      this.model = model;

      console.log('show add modal:', this.model.toJSON());
      this.render();

      this.$el.modal('show');
    },

    saveEditTitle: function() {

      var title = $('input.branch-title', this.el).val();

      this.model.set('title', title).trigger('change');

      console.log('save title', title);

      // vent.trigger('showAddBranchModal', this.model);
    },

    saveEditAddress: function() {

      var address = $('textarea.branch-address', this.el).val();

      this.model.set('address', address).trigger('change');

      // vent.trigger('showAddBranchModal', this.model);
    },

    saveEditHours: function() {

      var hours = $('textarea.branch-hours', this.el).val();

      this.model.set('hours', hours).trigger('change');
      // vent.trigger('showAddBranchModal', this.model);
    },

    saveEditTelephone: function() {

      var telephone = $('input.branch-telephone', this.el).val();

      this.model.set('telephone', telephone).trigger('change');

      // vent.trigger('showAddBranchModal', this.model);
    },

    saveEditLocation: function() {

      var lat = parseFloat($('input.lat', this.el).val()) || 0;
      var lng = parseFloat($('input.lng', this.el).val()) || 0;

      this.model.set('location', { '0': lat, '1':lng }).trigger('change');

      // vent.trigger('showAddBranchModal', this.model);
    },


    showEditPhoto: function() {
      console.log('show edit photo');
      $('div.edit-photo', this.el).show();
    },

    saveEditPhoto: function() {
      $('div.edit-photo', this.el).hide();

      var photo = $('input.branch-photo', this.el).val();

      this.model.set('photo', photo).trigger('change');
      // $('img.branch-photo').attr('src', photo)

      vent.trigger('showAddBranchModal', this.model);
    },

    createBranch: function() {
      this.$('div.edit-title').parent().removeClass('error');

      console.log('create branch!');
      this.model.set('company_id', parseInt(window.Company.companyId, 10));

      if(this.model.get('title').length == 0){
        alert('Please insert branch name');
        this.$('div.edit-title').parent().addClass('error');
        return;
      }
      sandbox.collections.branchCollection.create(this.model, {
        success: function() {
          //Refresh
          // window.location = window.Company.BASE_URL + 'r/company/' + window.Company.companyId +'/branch';
        }
      });

      this.$el.modal('hide');
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

            vent.trigger('showAddBranchModal', self.model);
            return;
          }
          alert(resp.data);
        }
      })
    },

    useGoogleMapsLink: function(e) {
      e.preventDefault();

      var link = this.$('input.google-maps-link').val()
        , latlng = getParameterByName(link, 'q').split(',')
        , lat = latlng[0]
        , lng = latlng[1]

      if(!lat || !lng) {
        latlng = getParameterByName(link, 'll').split(',')
        lat = latlng[0]
        lng = latlng[1]
      }

      if(!lat || !lng) {
        latlng = getParameterByName(link, 'sll').split(',')
        lat = latlng[0]
        lng = latlng[1]
      }

      if(lat && lng) {
        this.$('input.lat').val(lat)
        this.$('input.lng').val(lng)
        this.viewGoogleMaps(e)
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
        self.$('#gmaps').css({
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
