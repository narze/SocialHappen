require([
  'jquery',
  'moment'
], function($, moment){

  checkActionDone();
  formatDate();
  checkChallengeError();
  $('.load-more-in-progress').click(loadMoreInProgress);
  $('.load-more-completed').click(loadMoreCompleted);

  function checkActionDone() {
    if(action_done) {
      var actionDoneDiv = $('<div class="action-done alert alert-info"><strong>Action done!</strong></div>').hide();
      $('.page-header').before(actionDoneDiv);
      actionDoneDiv.delay(1000).slideDown();
    }
  }

  function checkChallengeError() {
    if(challengeError) {
      var message;
      if(challengeError === 'not_started') {
        message = 'Challenge not yet started';
      } else if(challengeError === 'ended') {
        message = 'Challenge ended already';
      }

      var challengeErrorDiv = $('<div class="action-done alert alert-error"><strong>'+message+'</strong></div>').hide();
      $('.page-header').before(challengeErrorDiv);
      challengeErrorDiv.delay(1000).slideDown();
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
  
  function loadMoreInProgress() {
    var limit = getMoreLimit;
    var offset = challengeInProgressIndex;
    var inProgressTemplate = _.template($('#challengers-item-template').html());
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: base_url + 'apiv3/get_challengers/' + challengeHash + '/' + limit + '/' + offset,
      success: function (resp) {
        _.each(resp.in_progress, function(user) {
          $('.challengers-in-progress').append(inProgressTemplate(user));
        });
      }
    });
    challengeInProgressIndex = offset + limit;
  }
  
  function loadMoreCompleted() {
    var limit = getMoreLimit;
    var offset = challengeCompletedIndex;
    var completedTemplate = _.template($('#challengers-item-template').html());
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: base_url + 'apiv3/get_challengers/' + challengeHash + '/' + limit + '/' + offset,
      success: function (resp) {
        _.each(resp.completed, function(user) {
          $('.challengers-completed').append(completedTemplate(user));
        });
      }
    });
    challengeCompletedIndex = offset + limit;
  }
});
