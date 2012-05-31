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

  function getUrlVars()
  {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
  }

  function checkActionDone() {
    var vars = getUrlVars();
    if(vars.action_done) {
      var actionDoneDiv = $('<div class="action-done alert alert-info"><strong>Action done!</strong></div>').hide();
      $('.page-header').before(actionDoneDiv);
      actionDoneDiv.delay(1000).slideDown();
    }
  }
});