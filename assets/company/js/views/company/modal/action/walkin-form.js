define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/WalkinEditTemplate.html',
  'chosen'
], function($, _, Backbone, walkinEditTemplate, chosen){
  var WalkinFormView = Backbone.View.extend({

    walkinEditTemplate: _.template(walkinEditTemplate),

    events: {
      'click button.save': 'saveEdit',
      'click button.generate-sonar-data': 'generateSonarData',
      'click button.cancel': 'cancelEdit',
      'keyup input.google-maps-link': 'useGoogleMapsLink',
      'click button.new-code': 'onAddNewCode',
      'click a.remove-code': 'onRemoveCode',
      'click button.new-location': 'onAddNewLocation',
      'click a.remove-location': 'onRemoveLocation'
    },

    initialize: function(){
      _.bindAll(this);
      sandbox.collections.deviceCollection.bind('reset', this.render);
      sandbox.collections.deviceCollection.fetch();
    },

    render: function () {
      var data = $.extend(true, {}, this.options.action);

      // data.deviceList = sandbox.collections.deviceCollection.toJSON();

      // console.log(data);

      $(this.el).html(this.walkinEditTemplate(data));

      // this.$('select.select-device').val(data.sonar_boxes);

      // setTimeout(function(){
      //   $('.select-device.chzn-select').chosen();
      // }, 100);

      return this;
    },

    showEdit: function(){
      $(this.el).modal('show');
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
        // this.viewGoogleMaps(e)
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

    onAddNewLocation: function(e){
      var lat = $.trim(this.$('input.lat').val());
      var lng = $.trim(this.$('input.lng').val());

      this.$('input.lat').val('');
      this.$('input.lng').val('');
      this.$('input.google-maps-link').val('');

      console.log('add location ', lat, lng);

      if(lat && lng){
        this.$('ul.locations').append($('<li data-lat="'+lat+'" data-lng="'+lng+'">'+lat+', '+lng+' <a href="#" class="remove-location">remove</a></li>'));
      }
    },

    onRemoveLocation: function(e){
      e.preventDefault();
      $(e.currentTarget).parent().remove();
    },

    onAddNewCode: function(e){
      var code = $.trim(this.$('input.code').val());
      this.$('input.code').val('');
      if(code){
        this.$('ul.codes').append($('<li data-code="'+code+'">'+code+' <a href="#" class="remove-code">remove</a></li>'));
      }
    },

    onRemoveCode: function(e){
      e.preventDefault();
      $(e.currentTarget).parent().remove();
    },

    generateSonarData: function() {
      var self = this
      $.ajax({
        type: 'GET',
        url: window.Company.BASE_URL + 'apiv3/get_sonar_box_data',
        dataType: 'JSON',
        success: function(res) {
          if(res.success) {
            $('.sonar-frequency', self.el).val(res.data)
          }
        }
      })
    },

    showEditName: function(){
      $('h3.edit-name', this.el).hide();
      $('div.edit-name', this.el).show();
      $('input.challenge-name', this.el).focus();
    },

    saveEdit: function(e){
      e.preventDefault();

      this.options.action.name = $('input.name', this.el).val();
      this.options.action.description = this.$('textarea.description').val();

      // var devices = this.$('select.select-device').val();

      // this.options.action.sonar_boxes = devices;

      // this.options.action.codes = _.map(this.$('select.select-device option:selected'), function(code){
      //   return $(code).attr('data-data');
      // }) || [];
      this.options.action.codes = _.map($('ul.codes li'), function(code){
        return $(code).attr('data-code');
      }) || [];

      this.options.action.locations = _.map($('ul.locations li'), function(code){
        var lat = $(code).attr('data-lat');
        var lng = $(code).attr('data-lng');
        return [lng, lat];
      }) || [];

      var criteria = this.model.get('criteria');

      if(this.options.save){
        for(var i = criteria.length - 1; i >= 0; i--) {
          var actionItem = criteria[i];

          if(actionItem.action_data_id == this.options.action.action_data_id){
            console.log('found action to save', criteria[i]);
            criteria[i] = _.clone(this.options.action);
            console.log('criteria to be saved', criteria);
            break;
          }
        };
      }

      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },

    cancelEdit: function(e){
      e.preventDefault();
      this.model.trigger('change');
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
  });
  return WalkinFormView;
});