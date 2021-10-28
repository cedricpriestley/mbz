<?php

namespace Drupal\mbz_migrate;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for gender entity.
 *
 * @ingroup mbz_migrate
 */
class GenderListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = [
      '#markup' => $this->t(''),
    ];

    $build += parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the gender
   * 
   * 
   * 
   * 
   * 
   *  list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['ID'] = $this->t('ID');
    $header['Name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\mbz_migrate\Entity\Gender */
    $row['id'] = $entity->id();
    /* $row['name'] = $entity->label(); */
    $row['name'] = $entity->toLink()->toString();
    return $row + parent::buildRow($entity);
  }

}
?>
