<?php

namespace Drupal\mbz_migrate\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the Gender entity.
 *
 * @ingroup mbz_migrate
 *
 * @ContentEntityType(
 *   id = "gender",
 *   label = @Translation("Gender"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mbz_migrate\GenderListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "storage" = "Drupal\mbz_migrate\GenderStorage",
 *       "add" = "Drupal\mbz_migrate\Form\GenderForm",
 *       "edit" = "Drupal\mbz_migrate\Form\GenderForm",
 *       "delete" = "Drupal\mbz_migrate\Form\GenderDeleteForm",
 *     },
 *     "access" = "Drupal\mbz_migrate\GenderAccessControlHandler",
 *   },
 *   base_table = "gender",
 *   admin_permission = "administer gender entity",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/music/gender/{gender}",
 *     "edit-form" = "/admin/structure/music/gender/{gender}/edit",
 *     "delete-form" = "/admin/structure/music/gender/{gender}/delete",
 *     "collection" = "/admin/structure/music/gender/list"
 *   },
 * )
 *
 */
class Gender extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Gender entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Gender entity.'))
      ->setReadOnly(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Gender entity.'))
      ->addConstraint('UniqueField', [
        'message' => 'A gender with name %value already exists.'
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
      ->setDescription(t('The description of the Gender entity.'))
      ->addConstraint('UniqueField', [
        'message' => 'A gender with description %value already exists.'
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
