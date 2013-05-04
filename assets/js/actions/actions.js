var ShFeedback = {

  init: function () {
    ShFeedback.bindPagination();
  },

  bindMoment: function () {
    $('.moment').each(function () {
      timeago = moment.unix($(this).text());
      $(this).html(timeago.fromNow());
    });
  },

  bindPagination: function () {
    var action_data_id = $('#action_data_id').val();
    var feedbacks_per_page = $('#feedbacks_per_page').val();

    $.getJSON(base_url+"actions/feedback/json_count_feedbacks/"+action_data_id, function(count){
      $('#feedbacks_pagination').pagination(count, {
        items_per_page:feedbacks_per_page,
        callback:ShFeedback.get_feedbacks,
        load_first_page:true,
        next_text: null,
        prev_text: null
      });
    });
  },

  changePaginationTags: function () {
    console.log('changePaginationTags', $('#feedbacks_pagination'));
    var current_page = $('#feedbacks_pagination').find('.current').text();

    $('#feedbacks_pagination').find('a').wrap('<li></li>');
    $('#feedbacks_pagination').find('.current').replaceWith('<li class="active"><a href="#">' + current_page + '</a></li>');
    $('#feedbacks_pagination').find('.pagination').addClass('pagination-centered').children().wrapAll("<ul></ul>");
    
  },

  get_feedbacks: function (page_index) {
    var feedbacks_pagination = $('#feedbacks_pagination');
    // set_loading();

    var action_data_id = $('#action_data_id').val();
    var feedbacks_per_page = $('#feedbacks_per_page').val();
    var url = base_url+'actions/feedback/json_get_feedbacks/'+action_data_id+'/'+feedbacks_per_page+'/'+(page_index * feedbacks_per_page);
    $.ajax({
      url: url,
      success: function (data) {
        $('.feedbacks-list').replaceWith($(data).filter('.feedbacks-list'));
        ShFeedback.bindMoment();
      }
    });
    
    if(feedbacks_pagination.find('a').attr('href') == '#') { 
        feedbacks_pagination.find('a').removeAttr('href'); // Remove href="#"
    }
                
    if(feedbacks_pagination.find('a').length == 0) {
        feedbacks_pagination.find('div.pagination').remove();
    }
    ShFeedback.changePaginationTags();
  }

};

$(function() {
    ShFeedback.init();
});