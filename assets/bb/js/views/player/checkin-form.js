define([
  'jquery',
  'underscore',
  'backbone',
  'text!templates/player/checkin-form.html',
  'jfmfs'
], function($, _, Backbone, formTemplate, jfmfs){
  var AppView = Backbone.View.extend({
    formTemplate: _.template(formTemplate),
    events: {
      'click button.submit': 'formSubmit'
    },
    initialize: function () {
      _.bindAll(this);
    },
    render: function () {
      //Load jfmfs css
      var link = document.createElement("link");
      link.type = "text/css";
      link.rel = "stylesheet";
      link.href = base_url + 'assets/css/common/jquery.facebook.multifriend.select-list.css';
      document.getElementsByTagName("head")[0].appendChild(link);

      //Load custom css
      link = document.createElement("link");
      link.type = "text/css";
      link.rel = "stylesheet";
      link.href = base_url + 'assets/css/common/jquery.jfmfs-custom.css';
      document.getElementsByTagName("head")[0].appendChild(link);

      $(this.el).html(this.formTemplate(this.options.challengeAction));
      $(this.el).slideDown();

      checkFBConnected(loadJFMFS);
      
      function loadJFMFS() {
        $("#jfmfs-container", this.el).jfmfs({
          max_selected: 15,
          max_selected_message: "{0} of {1} selected",
          friend_fields: "id,name,last_name",
          //pre_selected_friends: [1014025367],
          //exclude_friends: [1211122344, 610526078],
          sorter: function(a, b) {
            var x = a.last_name.toLowerCase();
            var y = b.last_name.toLowerCase();
            return ((x < y) ? -1 : ((x > y) ? 1 : 0));
          }
        });
      }
    },
    formSubmit: function() {
      if($("#jfmfs-container", $el).data('jfmfs').getSelectedIds().length < $('#checkin_min_friend_count', $el).val()) {
        alert('Please tag at least ' + $('#checkin_min_friend_count').val() + ' friend(s).');
        return false;
      }
      var $el = $(this.el);
      var data = {
        post_message: $('.post_message', $el).val(),
        tagged_user_facebook_ids: $("#jfmfs-container", $el).data('jfmfs').getSelectedIds().join(','),
        action_data_hash: $('.action_data_hash', $el).val()
      };
      
      $.ajax({
        type: 'POST',
        data: data,
        url: $('div.form', $el).data('action'),
        success: function(result) {
          console.log(result);
          $el.html(result);
        }
      });
    }
  });
  return AppView;
});
