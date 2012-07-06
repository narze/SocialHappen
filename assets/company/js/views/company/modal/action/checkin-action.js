define([
  'jquery',
  'underscore',
  'backbone',
  'views/company/modal/action/checkin-form',
  'text!templates/company/modal/action/CheckinAddTemplate.html',
  'text!templates/company/modal/action/placeItemTemplate.html',
  'text!templates/company/modal/action/CheckinActionTemplate.html'
], function($, _, Backbone, CheckinFormView, CheckinTemplate, placeItemTemplate, CheckinActionTemplate){
  var CheckinAddView = Backbone.View.extend({
    CheckinTemplate: _.template(CheckinTemplate),
    placeItemTemplate: _.template(placeItemTemplate),
    CheckinActionTemplate: _.template(CheckinActionTemplate),
    tagName: 'li',
    
    events: {
      'click .edit-action': 'showEdit',
      'click .remove-action': 'remove',
      'keyup input.checkin_facebook_place_name': 'searchPlace',
      'click a.place-item': 'selectPlace'
    },
    
    initialize: function(){
      _.bindAll(this);

      //Add action into model
      if(this.options.add) {
        var criteria = this.model.get('criteria');
      
        criteria.push(this.options.action);
        
        this.model.set('criteria', criteria).trigger('change');
        if(this.options.save){
          this.model.save();
        }
      }
    },
    
    render: function () {
      $(this.el).html(this.CheckinActionTemplate(this.options.action));
      return this;
    },
    
    showEdit: function(){
      var checkinFormView = new CheckinFormView({
        model: this.model,
        action: this.options.action,
        vent: this.options.vent,
        triggerModal: this.options.triggerModal,
        save: this.options.save
      });
      $('#action-modal').html(checkinFormView.render().el);
      $('#action-modal').modal('show');
    },
    
    searchPlace: function(e){
      var query = $('input.checkin_facebook_place_name').val();
      
      if(query.length === 0){
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
      
    },

    remove: function(e) {
      e.preventDefault();
      var criteria = this.model.get('criteria');
      var removeIndex = $(e.currentTarget).parents('ul.criteria-list > li').index();
      delete criteria[removeIndex];
      criteria = _.compact(criteria);

      this.model.set('criteria', criteria).trigger('change');
      if(this.options.save){
        this.model.save();
      }
      this.options.vent.trigger(this.options.triggerModal, this.model);
    }
  });
  return CheckinAddView;
});
