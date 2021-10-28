<?php

namespace Drupal\mbz_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DefaultController.
 */
class ArtistImport extends ControllerBase {
  /**
   * index.
   *
   * @return string
   *   Return index string.
   */
  public function index() {

    $build = [];
    $builtForm = \Drupal::formBuilder()->getForm('Drupal\mbz_migrate\Form\ArtistImportForm');
    $build['form'] = $builtForm;

    return $build;
  }
}
