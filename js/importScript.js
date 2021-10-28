/**
 * @file
 */

(function ($, Drupal) {
  Drupal.AjaxCommands.prototype.populateArtistForm = function (ajax, response, status) {
    let data = response.data
    $('#edit-name-0-value').val(data['name'])
    $('#edit-sort-name-0-value').val(data['sort-name'])
    $('#edit-disambiguation-0-value').val(data['disambiguation'])
    document.getElementById('edit-gender-id').value = data['gender']
    $('#edit-gender-id option[value=' + data['gender_id'] + ']').attr('selected','selected');
    $('#edit-type-id-0-target-id').val(data['type'])
  }

})(jQuery, Drupal);
