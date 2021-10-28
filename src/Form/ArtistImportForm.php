<?php

namespace Drupal\mbz_migrate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\mbz_migrate\Ajax\ImportCommand;

use Drupal\mbz_migrate\Entity\Artist;

/**
 * Class ArtistMigrationForm.
 */
class ArtistImportForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'artist_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    // Gather the number of names in the form already.
    $num_names = $form_state->get('num_names');
    // We have to ensure that there is at least one name field.
    if ($num_names === NULL) {
      $name_field = $form_state->set('num_names', 1);
      $num_names = 1;
    }

    $form['#tree'] = TRUE;
    $form['names_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Search Artists'),
      '#prefix' => '<div id="names-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_names; $i++) {
      $form['names_fieldset']['name'][$i] = [
        '#type' => 'textfield',

        '#title' => $this->t('Name'),
        '#description' => $this->t('The MusicBrainz ID for the artist to import'),
        '#autocomplete_route_name' => 'entity.artist.search_ajax.name',
        '#ajax' => [
          'callback' => '::searchArtist',
        ]
      ];
    }

    $form['names_fieldset']['actions'] = [
      '#type' => 'actions',
    ];

    $form['names_fieldset']['actions']['add_name'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add artist'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'names-fieldset-wrapper',
      ],
    ];
    // If there is more than one name, add the remove button.
    if ($num_names > 1) {
      $form['names_fieldset']['actions']['remove_name'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove one'),
        '#submit' => ['::removeCallback'],
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => 'names-fieldset-wrapper',
        ],
      ];
    }

    $form['artist_import'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Import'),
      '#weight' => 0,
      '#ajax' => [
        'callback' => '::fetchArtist',
      ]
    );

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function searchArtist(array $form, FormStateInterface $form_state)
  {

    $entityArr = $form_state->getUserInput();

    $artist_name = $entityArr['artist_search'][0]['value'];

    $data = mbz_migrate_artist_search($artist_name);

    $response = new AjaxResponse();
    $response->addCommand(new ImportCommand($data));
    return $response;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function fetchArtist(array $form, FormStateInterface $form_state)
  {

    $formValues = $form_state->getUserInput();

    $artistNames = $formValues['names_fieldset']['name'];
    $artist_mbids = [];

    foreach ($artistNames as $artistName) {
      $artist_mbids[] = explode("::", $artistName)[1];
    }

    foreach ($artist_mbids as $artist_mbid) {
      $data = mbz_migrate_artist_lookup($artist_mbid);

      $results = \Drupal::entityQuery('artist')
        ->condition('mbid', $artist_mbid, '=')
        ->execute();

      if (sizeof($results) === 0) {
        $artist_type_id = mbz_migrate_artist_type_id_get($data['type']);
        $gender_id = mbz_migrate_gender_id_get($data['gender']);

        $artist = Artist::create([
          'mbid' => $data['id'],
          'name' => $data['name'],
          'sort_name' => $data['sort-name'],
          'disambiguation' => $data['disambiguation'],
          'type_id' => $artist_type_id,
          'gender_id' => $gender_id
        ]);

        $artist->save();
      } elseif (sizeof($results) === 1) {
        $artist_type_id = mbz_migrate_artist_type_id_get($data['type']);
        $gender_id = mbz_migrate_gender_id_get($data['gender']);

        $artist = Artist::load(key($results));
        $artist->mbid = $data['id'];
        $artist->name = $data['name'];
        $artist->sort_name = $data['sort-name'];
        $artist->disambiguation = $data['disambiguation'];
        $artist->type_id = $artist_type_id;
        $artist->gender = $gender_id;

        $artist->save();
      } elseif (sizeof($results) > 1) {
        // log message "duplicate artist exists"
        $message = "";
        \Drupal::logger('mbz_migrate')->error($message);
      } elseif (!is_numeric($results)) {
        // log message "error loading artist entity"
        $message = "";
        \Drupal::logger('mbz_migrate')->error($message);
      }
    }

    $response = new AjaxResponse();
    $response->addCommand(new ImportCommand("{ message: \"data fetched\" }"));
    return $response;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state)
  {
    return $form['names_fieldset'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOne(array &$form, FormStateInterface $form_state)
  {
    $name_field = $form_state->get('num_names');
    $add_button = $name_field + 1;
    $form_state->set('num_names', $add_button);
    // Since our buildForm() method relies on the value of 'num_names' to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state)
  {
    $name_field = $form_state->get('num_names');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_names', $remove_button);
    }
    // Since our buildForm() method relies on the value of 'num_names' to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state->setRebuild();
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format' ? $value['value'] : $value));
    }
  }
}