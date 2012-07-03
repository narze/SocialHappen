define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/CheckinEditTemplate.html',
  'text!templates/company/modal/action/placeItemTemplate.html'
], function($, _, Backbone, CheckinTemplate, placeItemTemplate){
  var CheckinEditView = Backbone.View.extend({
    CheckinTemplate: _.template(CheckinTemplate),
    placeItemTemplate: _.template(placeItemTemplate),
    tagName: 'li',
    
    events: {
      'click button.edit': 'showEdit',
      'click button.save': 'saveEdit',
      'click button.remove': 'removeAction',
      'keyup input.checkin_facebook_place_name': 'searchPlace',
      'click a.place-item': 'selectPlace'
    },
    
    initialize: function(){
      _.bindAll(this);
    },
    
    render: function () {
      $(this.el).html(this.CheckinTemplate(this.options.action));
      
      return this;
    },
    
    showEdit: function(){
      $('div.edit', this.el).toggle();
    },
    
    saveEdit: function(e){
      e.preventDefault();
      $('div.edit', this.el).hide();
      
      var dataId = this.options.action.action_data_id;
      
      this.options.action.name = $('input.name', this.el).val();
      this.options.action.action_data.data.checkin_facebook_place_id = $('input.checkin_facebook_place_id', this.el).val();
      this.options.action.action_data.data.checkin_facebook_place_name = $('input.checkin_facebook_place_name', this.el).val();
      this.options.action.action_data.data.checkin_min_friend_count = $('input.checkin_min_friend_count', this.el).val();
      this.options.action.action_data.data.checkin_welcome_message = $('textarea.checkin_welcome_message', this.el).val();
      this.options.action.action_data.data.checkin_challenge_message = $('textarea.checkin_challenge_message', this.el).val();
      this.options.action.action_data.data.checkin_thankyou_message = $('textarea.checkin_thankyou_message', this.el).val();
      
      var criteria = this.model.get('criteria');
      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },
    
    removeAction: function(e){
      e.preventDefault();
      
      var dataId = this.options.action.action_data_id;
      var criteria = this.model.get('criteria');
      
      var target = $(e.currentTarget).parent().parent().parent().parent();
      var index = target.index();
            
      delete criteria[index];
      criteria = _.compact(criteria);
      
      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    },
    
    searchPlace: function(e){
      var query = $('input.checkin_facebook_place_name').val();
      
      if(query.length == 0){
        this.renderPlaceList([]);
      }else{
        var self = this;
        FB.api('/search?q='+encodeURIComponent(query)+'&type=place&access_token=' + FB.getAccessToken(), function(data) {
          self.renderPlaceList(data.data||[]);
        });
      }
    },
    
    renderPlaceList: function(data){
      $('ul.place-list', this.el).html('');
      if(data.length > 0){
        data = data.length > 5 ? data.slice(0, 5) : data;
        console.log(data);
        _.each(data, function(i){
          $('ul.place-list', this.el).append(this.placeItemTemplate(i));
        }, this);
      }
      
    },
    
    selectPlace: function(e){
      e.preventDefault();
      var id = $(e.currentTarget).data('id');
      var name = $(e.currentTarget).data('name');
      $('input.checkin_facebook_place_id').val(id);
      $('input.checkin_facebook_place_name').val(name);
      
    }
    
    
  });
  return CheckinEditView;
});