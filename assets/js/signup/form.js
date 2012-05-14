$(function(){
  //fill user timezone into form
  var user_timezone = 'UTC';
  if(typeof jstz !== 'undefined'){
    user_timezone = jstz.determine_timezone().name();
  }
  $('#signup-form input#timezone').val(user_timezone);
});