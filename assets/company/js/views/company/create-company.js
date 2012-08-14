define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/company/create-company.html'
], function($, _, Backbone, createCompanyTemplate){
  var CreateCompanyView = Backbone.View.extend({

    events: {
      'click button.create-company': 'createCompany'
    },

    initialize: function(){

    },

    render: function () {
      $(this.el).html(_.template(createCompanyTemplate)({}));
      return this;
    },

    createCompany: function(e) {
      e.preventDefault();
      var arr = $('form.create-company').serializeArray();
      var data = {
        company:  _(arr).reduce(function(acc, field) {
          acc[field.name] = field.value;
          return acc;
        }, {})
      }

      $.ajax({
        url: window.Company.BASE_URL + 'apiv3/createCompany',
        type: "POST",
        dataType: "json",
        data: data,
        success:function(resp){
          if(resp.success) {
            window.location = window.Company.BASE_URL + 'company/redirect/' + resp.data.company_id
          } else {
            alert(resp.data)
          }
        }
      })
    }

  });
  return CreateCompanyView;
});
