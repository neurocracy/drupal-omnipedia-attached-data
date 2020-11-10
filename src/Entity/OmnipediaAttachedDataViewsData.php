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

    $data['omnipedia_attached_data']['omnipedia_attached_data_bulk_form'] = [
      'title' => $this->t('Omnipedia attached data operations bulk form'),
      'help'  => $this->t('Add a form element that lets you run operations on multiple attached data.'),
      'field' => [
        'id'    => 'omnipedia_attached_data_bulk_form',
      ],
    ];

    return $data;
  }

}
