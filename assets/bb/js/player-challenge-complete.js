require([
  'jquery',
  'bootstrap'
], function($, Bootstrap){
  
  if(challenge_done) {
    $('#challenge-complete-modal').modal();
  }
});
