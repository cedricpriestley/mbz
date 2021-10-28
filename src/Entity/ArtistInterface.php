<?php

namespace Drupal\mbz_migrate\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Artist entity entities.
 *
 * @ingroup mbz_migrate
 */
interface ArtistInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Artist entity name.
   *
   * @return string
   *   Name of the Artist entity.
   */
  public function getName();

  /**
   * Sets the Artist entity name.
   *
   * @param string $name
   *   The Artist entity name.
   *
   * @return \Drupal\mbz_migrate\Entity\ArtistInterface
   *   The called Artist entity entity.
   */
  public function setName($name);

  /**
   * Gets the Artist entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Artist entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Artist entity creation timestamp.
   *
   * @param int $timestamp
   *   The Artist entity creation timestamp.
   *
   * @return \Drupal\mbz_migrate\Entity\ArtistInterface
   *   The called Artist entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Artist entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Artist entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\mbz_migrate\Entity\ArtistInterface
   *   The called Artist entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Artist entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Artist entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\mbz_migrate\Entity\ArtistInterface
   *   The called Artist entity entity.
   */
  public function setRevisionUserId($uid);

}
