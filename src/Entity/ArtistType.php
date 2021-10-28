<?php

namespace Drupal\mbz_migrate\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the ArtistType entity.
 *
 * @ingroup mbz_migrate
 *
 * @ContentEntityType(
 *   id = "artist_type",
 *   label = @Translation("Artist Type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mbz_migrate\ArtistTypeListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\mbz_migrate\Form\ArtistTypeForm",
 *       "add" = "Drupal\mbz_migrate\Form\ArtistTypeForm",
 *       "edit" = "Drupal\mbz_migrate\Form\ArtistTypeForm",
 *       "delete" = "Drupal\mbz_migrate\Form\ArtistTypeDeleteForm",
 *     },
 *     "access" = "Drupal\mbz_migrate\ArtistTypeAccessControlHandler",
 *   },
 *   base_table = "artist_type",
 *   admin_permission = "administer artist_type entity",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/music/artist_type/{artist_type}",
 *     "edit-form" = "/admin/structure/music/artist_type/{artist_type}/edit",
 *     "delete-form" = "/admin/structure/music/artist_type/{artist_type}/delete",
 *     "collection" = "/admin/structure/music/artist_type/list"
 *   },
 * )
 *
 */
class ArtistType extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the ArtistType entity.'))
      ->setReadOnly(TRUE);

      $fields['vid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Version ID'))
      ->setDescription(t('The Version ID of the ArtistType entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the ArtistType entity.'))
      ->setReadOnly(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the ArtistType entity.'))
      ->addConstraint('UniqueField', [
        'message' => 'An artist type with name %value already exists.'
      ])
      ->setSettings(array(
        'max_length' => 255,
      ))
      ->setDisplayOptions('view', array(
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setDescription(t('The description of the ArtistType entity.'))
      ->addConstraint('UniqueField', [
        'message' => 'An artist type with description %value already exists.'
      ])
      ->setSettings(array(
        'max_length' => 255,
      ))
      ->setDisplayOptions('view', array(
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }
}

?>
