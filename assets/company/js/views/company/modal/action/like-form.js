define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/action/LikeEditTemplate.html'
], function($, _, Backbone, likeEditTemplate){
  var LikeFormView = Backbone.View.extend({

    likeEditTemplate: _.template(likeEditTemplate),

    events: {
      'click button.save': 'saveEdit',
      'click button.cancel': 'cancelEdit',
      'click button.get-facebookid': 'onClickGetFacebookId'
    },

    initialize: function(){
      _.bindAll(this);
    },

    render: function () {
      var data = this.options.action;

      $(this.el).html(this.likeEditTemplate(data));

      if((typeof data.page_name != 'undefined') && data.page_name){
        this.$('div.page_name').show();
      }else{
        this.$('div.page_name').hide();
      }

      if((typeof data.facebook_id != 'undefined') && data.facebook_id){
        // this.$('div.facebook_id').show();
      }else{
        this.$('div.facebook_id').hide();
      }

      return this;
    },

    showEdit: function(){
      $(this.el).modal('show');
    },

    showEditName: function(){
      $('h3.edit-name', this.el).hide();
      $('div.edit-name', this.el).show();
      $('input.challenge-name', this.el).focus();
    },

    onClickGetFacebookId: function(){
      var url = $('input.url', this.el).val();

      if(url){
        var self = this;

        this.$('div.page_name').hide();
        // this.$('div.facebook_id').hide();

        $('input.page_name', this.el).val('');
        $('input.facebook_id', this.el).val('');

        FB.api({
          method: 'fql.query',
            query: 'SELECT url, id, type, site FROM object_url WHERE url="' + url + '"'
          },
          function(response) {
            console.log(response);
            if(response && response.length > 0 && response[0].type == 'page'){

              var page_id = response[0].id;

              FB.api({
                method: 'fql.query',
                  query: 'SELECT name FROM page WHERE page_id = ' + page_id
                },
                function(response) {
                  console.log(response);
                  if(response && response.length > 0 && response[0].name){
                    self.$('input.facebook_id').val(page_id);

                    self.$('input.page_name').val(response[0].name);
                    self.$('a.page_name').text(response[0].name);
                    self.$('a.page_name').attr('src', url);
                    self.$('div.page_name').show();
                    // this.$('div.facebook_id').show();
                  }else{
                    alert('get Facebook page name error, please enter a valid page url.');
                  }
                }
              );
            }else{
              alert('get Facebook page id error, please enter a valid page url.');
            }
          }
        );
      }
    },

    saveEdit: function(e){
      e.preventDefault();

      this.options.action.name = $('input.name', this.el).val();
      this.options.action.description = this.$('textarea.description').val();
      this.options.action.url = $('input.url', this.el).val();
      this.options.action.facebook_id = $('input.facebook_id', this.el).val();
      this.options.action.page_name = $('input.page_name', this.el).val();

      console.log('save  edit action', this.options.action);

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
  return LikeFormView;
});