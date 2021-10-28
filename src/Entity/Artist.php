<?php

namespace Drupal\mbz_migrate\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the Artist entity.
 *
 * @ingroup mbz_migrate
 *
 * @ContentEntityType(
 *   id = "artist",
 *   label = @Translation("Artist"),
 *   handlers = {
 *     "storage" = "Drupal\mbz_migrate\ArtistStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mbz_migrate\ArtistListBuilder",
 *     "views_data" = "Drupal\views\ArtistViewsData",
 *     "translation" = "Drupal\mbz_migrate\ArtistTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\mbz_migrate\Form\ArtistForm",
 *       "add" = "Drupal\mbz_migrate\Form\ArtistForm",
 *       "edit" = "Drupal\mbz_migrate\Form\ArtistForm",
 *       "delete" = "Drupal\mbz_migrate\Form\ArtistDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\mbz_migrate\ArtistHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\mbz_migrate\ArtistAccessControlHandler",
 *   },
 *   base_table = "artist",
 *   data_table = "artist_field_data",
 *   revision_table = "artist_revision",
 *   revision_data_table = "artist_field_revision",
 *   admin_permission = "administer artist entity",
 *   fieldable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *     "published" = "status",
 *     "uid" = "user_id",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/music/artist/{artist}",
 *     "add-form" = "/admin/structure/music/artist/add",
 *     "edit-form" = "/admin/structure/music/artist/{artist}/edit",
 *     "delete-form" = "/admin/structure/music/artist/{artist}/delete",
 *     "version-history" = "/admin/structure/artist/{artist}/revisions",
 *     "revision" = "/admin/structure/artist/{artist}/revisions/{artist_revision}/view",
 *     "revision_revert" = "/admin/structure/artist/{artist}/revisions/{artist_revision}/revert",
 *     "revision_delete" = "/admin/structure/artist/{artist}/revisions/{artist_revision}/delete",
 *     "translation_revert" = "/admin/structure/artist/{artist}/revisions/{artist_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/music/artist/list"
 *   },
 *   field_ui_base_route = "entity.artist.settings",
 * )
 *
 */
class Artist extends EditorialContentEntityBase implements ArtistInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }


  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly,
    // make the artist owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('integer')

      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Artist entity.'))
      ->setReadOnly(TRUE);

    $fields['vid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Version ID'))
      ->setDescription(t('The Version ID of the Artist entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Default entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

      $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Artist entity.'))
      ->setReadOnly(TRUE);

    $fields['mbid'] = BaseFieldDefinition::create('string')
      ->setLabel(t('MBID'))
      ->setDescription(t('The name of the Artist Type entity.'))
      ->addConstraint('UniqueField', [
        'message' => 'An artist with MBID %value already exists.'
      ])
      ->setSettings(array(
        'max_length' => 36,
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Artist entity.'))
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

    $fields['sort_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Sort Name'))
      ->setDescription(t('The sort name of the Artist entity.'))
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

    $fields['disambiguation'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Disambiguation'))
      ->setDescription(t('The disambiguity name of the Artist entity.'))
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

    $fields['type_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Type'))
      ->setDescription(t('The type of artist'))
      ->setSetting('target_type', 'artist_type')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', array(
        'type' => 'entity_reference_label',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['gender_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Gender'))
      ->setDescription(t('The id of the artists gender'))
      ->setSetting('target_type', 'gender')
      ->setSetting('handler', 'default')
      // ->setSettings(array(
      //   'allowed_values' => array(
      //     'Male' => 'Male',
      //     'Female' => 'Female',
      //     'Other' => 'Other',
      //     'Not applicable' => 'Not applicable',
      //   ),
      // ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'list_default',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // $fields['country'] = BaseFieldDefinition::create('entity_reference')
    //   ->setLabel(t('Country'))
    //   ->setSetting('target_type', 'taxonomy_term')
    //   ->setSetting('handler', 'default')
    //   ->setSetting('handler_settings',
    //   array(
    //     'target_bundles' => array(
    //       'cit_countries_information' => 'cit_countries_information'
    //     )))
    //     ->setDisplayOptions('view', array(
    //       'type' => 'author',
    //       'weight' => 0,
    //     ))
    //     ->setDisplayOptions('form', array(
    //       'type' => 'entity_reference_autocomplete',
    //       'weight' => 0,
    //       'settings' => array(
    //         'match_operator' => 'CONTAINS',
    //         /* 'size' => '10', */
    //         'autocomplete_type' => 'tags',
    //         'placeholder' => '',
    //       ),
    //     ))
    //     ->setDisplayConfigurable('form', TRUE)
    //     ->setDisplayConfigurable('view', TRUE);

    /* $fields['country'] = BaseFieldDefinition::create('entity_reference') */
    /*   ->setLabel(t('Country')) */
    /*   ->setDescription(t('The country of artist')) */
    /*   ->setSetting('target_type', 'country') */
    /*   ->setSetting('handler', 'default') */
    /*   ->setDisplayOptions('view', array( */
    /*     'type' => 'entity_reference_label', */
    /*     'weight' => 0, */
    /*   )) */
    /*   ->setDisplayOptions('form', array( */
    /*     'type' => 'entity_reference_autocomplete', */
    /*     'settings' => array( */
    /*       'match_operator' => 'CONTAINS', */
    /*       'size' => 60, */
    /*       'autocomplete_type' => 'tags', */
    /*       'placeholder' => '', */
    /*     ), */
    /*     'weight' => 0, */
    /*   )) */
    /*   ->setDisplayConfigurable('form', TRUE) */
    /*   ->setDisplayConfigurable('view', TRUE); */

    /* $fields['person_id'] = BaseFieldDefinition::create('entity_reference') */
    /*   ->setLabel(t('Real Name')) */
    /*   ->setDescription(t('The person behind the artist')) */
    /*   ->setSetting('target_type', 'artist') */
    /*   ->setSetting('handler', 'default') */
    /*   ->setDisplayOptions('view', array( */
    /*     'type' => 'entity_reference_label', */
    /*     'weight' => 0, */
    /*   )) */
    /*   ->setDisplayOptions('form', array( */
    /*     'type' => 'entity_reference_autocomplete', */
    /*     'settings' => array( */
    /*       'match_operator' => 'CONTAINS', */
    /*       'size' => 60, */
    /*       'autocomplete_type' => 'tags', */
    /*       'placeholder' => '', */
    /*     ), */
    /*     'weight' => 0, */
    /*   )) */
    /*   ->setDisplayConfigurable('form', TRUE) */
    /*   ->setDisplayConfigurable('view', TRUE); */

$fields['band_members'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Member(s)'))
      ->setDescription(t('The persons of the band'))
      ->setSetting('target_type', 'artist')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', array(
        'type' => 'entity_reference_label',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'autocomplete_type' => 'tags',
        ),
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Default entity is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE);

    return $fields;
  }
}
?>
