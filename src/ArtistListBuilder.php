<?php

namespace Drupal\mbz_migrate;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for artist entity.
 *
 * @ingroup mbz_migrate
 */
class ArtistListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = [
      '#markup' => $this->t('The Artist entity represents a musical artist. These artists are fieldable entities. You can manage the fields on the <a href="@adminlink">Artists admin page</a>.', array(
        '@adminlink' => \Drupal::urlGenerator()
          ->generateFromRoute('entity.artist.collection'),
      )),
    ];

    $build += parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the artist list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['ID'] = $this->t('id');
    /* $header['MBID'] = $this->t('mbid'); */
    $header['name'] = $this->t('name');
    $header['sort_name'] = $this->t('Sort Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\mbz_migrate\Entity\Artist */
    $row['id'] = $entity->id();
    /* $row['mbid'] = $entity->mbid(); */
    $row['name'] = $entity->toLink()->toString();
    $row['sort_name'] = $entity->sort_name->value;
    return $row + parent::buildRow($entity);
  }

}
?>
