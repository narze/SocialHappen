define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/modal/reward/edit-reward.html',
  'jqueryui'
], function($, _, Backbone, editTemplate, jqueryui){
  var EditModalView = Backbone.View.extend({
    editTemplate: _.template(editTemplate),
    events: {
      'click button.redeem-reward': 'redeemReward'
    },
    
    initialize: function(){
      _.bindAll(this);
      this.options.vent.bind('showEditRewardModal', this.show);
    },

    render: function () {
      console.log('render modal');
      
      if(!this.model){
        return;
      }
      
      var data = this.model.toJSON();
      console.log(data);

      data.passportCouponPageURL = window.Company.BASE_URL + 'passport/'
       + Company.currentUserModel.get('user_id');
      
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

    redeemReward: function() {
      var model = this.model;
      $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
          reward_item_id: model.id,
          company_id: window.Company.companyId
        },
        url: window.Company.BASE_URL + 'apiv3/purchaseReward',
        success: function(res) {
          if(res.success) {
            console.log('got coupon id : ' + res.data.coupon_id);
            $('button.redeem-reward', self.el).html('Redeemed').attr("disabled", "disabled");
          }else{
            alert(res.data);
          }
        }
      });
    }
  });
  return EditModalView;
});
