<?php

namespace Drupal\omnipedia_attached_data\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for the OmnipediaAttachedData entity.
 */
class OmnipediaAttachedDataViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    /** @var array */
    $data = parent::getViewsData();

    return $data;
  }

}
