<?php

namespace Drupal\omnipedia_attached_data\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a OmnipediaAttachedData entity.
 */
interface OmnipediaAttachedDataInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
