<?php

namespace Drupal\mbz_migrate\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\mbz_migrate\Ajax\ImportCommand;

/**
 * Form controller for Artist edit forms.
 *
 * @ingroup mbz_migrate
 */
class ArtistForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
    * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* dargs(); */
    /* dpm($form_state); */
    /* echo "hello";exit; */
    /* exit; */
    /* @var \Drupal\mbz_migrate\Entity\Artist $artist */
    $form = parent::buildForm($form, $form_state);
    if (!$this->entity->isNew()) {
      $form['new_revision'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Create new revision'),
        '#default_value' => FALSE,
        '#weight' => 10,
      ];
    }

    $form['#attached']['library'][] = "mbz_migrate/mbz_migrate-library";

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime($this->time->getRequestTime());
      $entity->setRevisionUserId($this->account->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Artist.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Artist.', [
          '%label' => $entity->label(),
        ]));
    }

    $form_state->setRedirect('entity.artist.collection');
    return $status;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions =  parent::actions($form, $form_state);
    $actions['artist_import'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Import'),
      //'#submit' => ['::fetchArtist'],
      '#weight' => -11,
      '#ajax' => [
        'callback' => '::fetchArtist',
      ]
    );
    return $actions;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function fetchArtist(array $form, FormStateInterface $form_state) {

    $entityArr = $form_state->getUserInput();
    $mbid = $entityArr['mbid'][0]['value'];
    $sort_name = $entityArr['sort_name'][0]['value'];

    $data = [];

    if (!empty($mbid) && empty($sort_name)) {
      $data = mbz_migrate_artist_lookup($mbid);
      $gender_id = mbz_migrate_gender_id_get($data['gender']);
      $data['gender_id'] = $gender_id;
    }

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
  public function importArtist(array $form, FormStateInterface $form_state) {

    $entityArr = $form_state->getUserInput();
    $mbid = $entityArr['mbid'][0]['value'];
    $sort_name = $entityArr['sort_name'][0]['value'];

    if (!empty($mbid) && empty($sort_name)) {

      $data = mbz_migrate_artist_lookup($mbid);

      if ($data) {
        $entityArr['name'][0]['value'] = $data['name'];
        $entityArr['sort_name'][0]['value'] = $data['sort-name'];
        $entityArr['type_id'][0]['target_id'] = $data['type'];

        $form_state->setUserInput($entityArr);
      }
    }

    $form_state->setRebuild(true);
    $form_state->disableRedirect(true);
  }
}
?>
