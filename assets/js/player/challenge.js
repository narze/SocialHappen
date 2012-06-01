$(function() {
  checkFBConnected(checkCallback);
  checkActionDone();

  function checkCallback(fbUserID) {
    $('#join-challenge').show();
    $('#join-challenge').click(function() {
      window.fbLogin(loginCallback);
    });
  }

  function loginCallback(loggedIn) {
    if(!loggedIn) {
      return;
    }

    return window.location = base_url + 'login?next=' + $('#join-challenge').data('url');
  }

  function checkActionDone() {
    if(action_done) {
      var actionDoneDiv = $('<div class="action-done alert alert-info"><strong>Action done!</strong></div>').hide();
      $('.page-header').before(actionDoneDiv);
      actionDoneDiv.delay(1000).slideDown();
    }
  }
});