$(function() {
  checkFBConnected(checkCallback);

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
});