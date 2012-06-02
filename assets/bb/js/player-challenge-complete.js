require([
  'jquery',
  'bootstrap'
], function($, Bootstrap){
  
  if(challenge_done) {
    $('#challenge-complete-modal').modal();
    shareChallengeComplete();
  }

  function shareChallengeComplete() {
    $('#share-challenge-complete').click(function() {
      var url = window.location.href;
      if(url.indexOf('?') !== -1) {
        url = url.substring(0, url.indexOf('?'));
      }

      FB.ui({
        method: 'feed',
        picture: null,
        name: 'SocialHappen Challenge Name',
        caption: 'SocialHappen',
        description: 'I\'ve completed a Challenge!!',
        link: url
      });
    });
  }
});
