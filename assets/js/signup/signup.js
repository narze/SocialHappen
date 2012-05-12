$(function(){
  //When FB init, show signup buttons
  checkFBConnected(function(){
    $('#login').show();
  });

  //redirect when connected facebook
  window.fbLoginResult = function(connected) {
    if(connected) {
      window.location = $('#facebook-connect').data('redirect');
    }
  };
});