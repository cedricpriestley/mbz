<?php

namespace Drupal\mbz_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DefaultController.
 */
class Music extends ControllerBase {
  /**
   * index.
   *
   * @return string
   *   Return index string.
   */
  public function index() {

    return [
      '#type' => 'markup',
      '#markup' => '<ul>
      <li><a href="/mbz/artist/list">Artists</a></li>
      <li><a href="/mbz/artist-type/list">Artist Types</a></li>
      <li><a href="/mbz/gender/list">Gender</a></li>
      <li><a href="/mbz/artist/import">Import</a></li>
      </ul>',
    ];

    return $build;
  }
}
