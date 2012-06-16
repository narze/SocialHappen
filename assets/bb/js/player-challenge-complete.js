require([
  'jquery',
  'bootstrap'
], function($){
    if(challenge_done) {
      $('#challenge-complete-modal').modal();
      shareChallengeComplete();
    }

    function shareChallengeComplete() {
      var url = window.location.href;
      if(url.indexOf('?') !== -1) {
        url = url.substring(0, url.indexOf('?'));
      }

      $('#share-challenge-complete').click(function() {
        FB.ui({
          method: 'feed',
          picture: null,
          name: 'SocialHappen Challenge Name',
          caption: 'SocialHappen',
          description: 'I\'ve completed a Challenge!!',
          link: url
        });
      });

      $('#challenge-complete-modal').on('hidden', function () {
        window.location = url;
      });
    }
});
