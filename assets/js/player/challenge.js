$(function() {
  var formLoaded;
  $('.criteria-link').toggle(loadCriteriaForm, hideCriteriaForm);

  // var criteriaFormTemplate = _.template($('#criteria-form-template').html());

  function loadCriteriaForm() {
    $form = $(this).parents('.criteria-item').find('.criteria-form');
    $form.hide();
    if(!formLoaded) {
      $.ajax({
        method: 'POST',
        url: base_url + $(this).data('url'),
        success: function(data) {
          $form.html(data);
          $form.fadeIn();
          formLoaded = true;
          bindFormSubmit($form);
        }
      });
    } else {
     $form.fadeIn();
    }
  }

  function hideCriteriaForm() {
    $form = $(this).parents('.criteria-item').find('.criteria-form');
    $form.fadeOut();
  }
  
  function bindFormSubmit(formDiv) {
    var form = $('form:first', formDiv);
    form.ajaxForm({
      target: formDiv,
      success: function(data) {
        console.log(data);
      }
      });
  }
});