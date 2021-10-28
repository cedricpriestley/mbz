<?php

namespace Drupal\mbz_migrate\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Client;

/**
 * Provides a 'ArtistImportBlock' block.
 *
 * @Block(
 *  id = "artist_import_block",
 *  admin_label = @Translation("Artist Import Block"),
 * )
 */
class ArtistImportBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'artist_get' => Get,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $builtForm = \Drupal::formBuilder()->getForm('Drupal\mbz_migrate\Form\ArtistImportForm');
    $build['form'] = $builtForm;

    return $build;
  }
}
