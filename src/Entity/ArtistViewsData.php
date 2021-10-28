<?php

namespace Drupal\mbz_migrate\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Artist entities.
 */
class ArtistViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
