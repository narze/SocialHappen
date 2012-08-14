require([
  'jquery',
  'moment',
  'underscore'
], function($, moment, _){

  var challengers = {

    challengersObj: null,

    limit: challengeInProgressIndex,

    addChallengers: function() {
      var self = this;
      $.ajax({
        type: 'POST',
        dataType: 'json',
        url: base_url + 'apiv3/get_challengers/' + challengeHash + '/' + 0 + '/' + challengeInProgressIndex,
        success: function (resp) {
          if(!self.challengersObj) {
            self.challengersObj = resp;
          }

          $('.load-more-in-progress').click(function() {self.loadMoreInProgress(self.challengersObj)} );
          $('.load-more-completed').click(function() {self.loadMoreCompleted(self.challengersObj)} );
        }
      });
    },

    addAll: function(challengersObj) {
      this.challengersObj = challengersObj;
      console.log(this.challengersObj);
      _.each(_.first(this.challengersObj.in_progress, this.limit), function(model){
        this.addOneInProgress(model);
      }, this);

      if((this.challengersObj.in_progress.length <= this.limit) || !this.limit){
        $('button.load-more-in-progress').addClass('hide');
      } else {
        $('button.load-more-in-progress').removeClass('hide');
      }

      _.each(_.first(this.challengersObj.completed, this.limit), function(model){
        this.addOneCompleted(model);
      }, this);

      if((this.challengersObj.completed.length <= this.limit) || !this.limit){
        $('button.load-more-completed').addClass('hide');
      } else {
        $('button.load-more-completed').removeClass('hide');
      }
    },

    addOneInProgress: function(model) {
      var challenger = _.template($('#challengers-item-template').html())(model)
      $('.challengers-in-progress').append(challenger);
    },

    addOneCompleted: function(model) {
      var challenger = _.template($('#challengers-item-template').html())(model)
      $('.challengers-completed').append(challenger);
    },

    loadMoreInProgress: function(challengersObj) {
      this.challengersObj = challengersObj
      this.challengersObj.in_progress = this.challengersObj.in_progress.slice(this.limit);
      return this.addAll(this.challengersObj);
    },

    loadMoreCompleted: function(challengersObj) {
      this.challengersObj = challengersObj
      this.challengersObj.completed = this.challengersObj.completed.slice(this.limit);
      return this.addAll(this.challengersObj);
    }
  }

  function checkActionDone() {
    if(action_done) {
      var actionDoneDiv = $('<div class="action-done alert alert-info"><strong>Action done!</strong></div>').hide();
      $('.page-header').before(actionDoneDiv);
      actionDoneDiv.slideDown();
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
      challengeErrorDiv.slideDown();
    }
  }

  function formatDate(format) {
    if(!format) format = "dddd, MMMM Do YYYY, h:mm:ss a";

    var startString = moment.unix(challenge_start_date).format(format);
    $('#challenge-start-date').html(startString);

    var endString = moment.unix(challenge_end_date).format(format);
    $('#challenge-end-date').html(endString);

    var untilEndString = moment.unix(challenge_end_date).fromNow();
    $('#challenge-until-end').html(untilEndString);
  }


  checkActionDone();
  formatDate("MMMM Do, YYYY");
  checkChallengeError();
  challengers.addChallengers();





  // function loadMoreInProgress() {
  //   var limit = getMoreLimit;
  //   var offset = challengeInProgressIndex;
  //   var inProgressTemplate = _.template($('#challengers-item-template').html());
  //   $.ajax({
  //     type: 'POST',
  //     dataType: 'json',
  //     url: base_url + 'apiv3/get_challengers/' + challengeHash + '/' + limit + '/' + offset,
  //     success: function (resp) {
  //       _.each(resp.in_progress, function(user) {
  //         $('.challengers-in-progress').append(inProgressTemplate(user));
  //       });
  //     }
  //   });
  //   challengeInProgressIndex = offset + limit;
  // }

  // function loadMoreCompleted() {
  //   var limit = getMoreLimit;
  //   var offset = challengeCompletedIndex;
  //   var completedTemplate = _.template($('#challengers-item-template').html());
  //   $.ajax({
  //     type: 'POST',
  //     dataType: 'json',
  //     url: base_url + 'apiv3/get_challengers/' + challengeHash + '/' + limit + '/' + offset,
  //     success: function (resp) {
  //       _.each(resp.completed, function(user) {
  //         $('.challengers-completed').append(completedTemplate(user));
  //       });
  //     }
  //   });
  //   challengeCompletedIndex = offset + limit;
  // }
});
