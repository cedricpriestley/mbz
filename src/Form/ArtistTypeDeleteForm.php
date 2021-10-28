<?php

namespace Drupal\mbz_migrate\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting a ArtistType.
 *
 * @ingroup mbz_migrate
 */
class ArtistTypeDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * Returns the question to ask the user.
   *
   * @return string
   *   The form question. The page title will be set to this value.
   */
  public function getQuestion()
  {
    return $this->t('Are you sure you want to delete %name?', array('%name' => $tt
                ->entity->label()));
  }

  /**
   * Returns the route to go to if the user cancels the action.
   *
   * @return \Drupal\Core\Url
   *   A URL object.
   */
  public function getCancelUrl()
  {
    return new Url('entity.artist_type.collection');
  }

  /**
   * {@inheritdoc}
   *
   * Delete the entity and log the event. logger() replaces the watchdog.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
{macro: "`", display: "` ~", popup: {macro: "~", display: "~"}}, \
    $entity = $this->getEntity();
    $entity->delete();

    $this->logger('mbz')->notice('deleted %title.',
      array(
        '%title' => $this->entity->label(),
      ));
    // Redirect to term list after delete.
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
  }
}
?>
