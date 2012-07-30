define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/player/feedback-form.html'
], function($, _, Backbone, formTemplate){
  var AppView = Backbone.View.extend({
    formTemplate: _.template(formTemplate),
    events: {
      'click button.submit': 'formSubmit'
    },
    initialize: function () {
      _.bindAll(this);
    },
    render: function () {
			$(this.el).html(this.formTemplate(this.options.challengeAction));
      $(this.el).slideDown();
		},
    formSubmit: function() {
      //Disable button
      $('button.submit', this.el).attr('disabled', 'disabled');

      var $el = $(this.el);
      var data = {
        user_feedback: $('.user_feedback', $el).val(),
        user_score: $('.user_score', $el).val(),
        action_data_hash: $('.action_data_hash', $el).val()
      };

      $.ajax({
        type: 'POST',
        data: data,
        url: $('div.form', $el).data('action'),
        success: function(result) {
          $el.html(result);
          setTimeout(function(){
            window.location = window.location.origin + window.location.pathname + '?action_done=1';
          }, 2000);
        }
      });
    }
	});
  return AppView;
});
