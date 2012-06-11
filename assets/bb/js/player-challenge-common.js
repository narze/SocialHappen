require([
  'jquery',
  'moment'
], function($, moment){

  checkActionDone();
  formatDate();

  function checkActionDone() {
    if(action_done) {
      var actionDoneDiv = $('<div class="action-done alert alert-info"><strong>Action done!</strong></div>').hide();
      $('.page-header').before(actionDoneDiv);
      actionDoneDiv.delay(1000).slideDown();
    }
  }

  function formatDate() {

    var startString = moment.unix(challenge_start_date).format("dddd, MMMM Do YYYY, h:mm:ss a");
    $('#challenge-start-date').html(startString);

    var endString = moment.unix(challenge_end_date).format("dddd, MMMM Do YYYY, h:mm:ss a");
    $('#challenge-end-date').html(endString);

    var untilEndString = moment.unix(challenge_end_date).fromNow();
    $('#challenge-until-end').html(untilEndString);
  }
});
