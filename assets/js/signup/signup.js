$(function(){
  //When FB init, show signup buttons
  checkFBConnected(function(){
    $('#login').show();
  });

  //redirect when connected facebook
  window.fbLoginResult = function(connected) {
    if(connected) {
      window.location = $('#signup-form-facebook-connect').data('redirect') + '&' + $('#signup-form-facebook-connect').data('next');
    }
  };

  //form 
  var user_timezone = 'UTC';
  if(typeof jstz !== 'undefined'){
    user_timezone = jstz.determine_timezone().name();
  }
  $('#signup-form input#timezone').val(user_timezone);
  var redirect_url = $('#signup-form-facebook-connect').data('redirect');
  $('#signup-form-facebook-connect').data('redirect', redirect_url + '?timezone=' + user_timezone);
});