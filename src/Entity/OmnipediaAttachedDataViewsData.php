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

    /** @var \Drupal\Core\StringTranslation\TranslatableMarkup */
    $groupName = $this->t('Omnipedia: Attached data');

    $data['omnipedia_attached_data']['omnipedia_attached_data_bulk_form'] = [
      'title' => $this->t('Bulk operations form'),
      'group' => $groupName,
      'help'  => $this->t('Add a form element that lets you run operations on multiple attached data.'),
      'field' => [
        'id'    => 'omnipedia_attached_data_bulk_form',
      ],
    ];

    $data['omnipedia_attached_data']['type']['group'] = $groupName;

    $data['omnipedia_attached_data']['type']['filter'] = [
      'title'   => $this->t('Type'),
      // This is the help text shown on the plug-in options modal in the Views
      // UI.
      'help'    => $this->t('Filter by attached data type.'),
      // This is the column in the table that this plug-in operates on.
      'field'   => 'type',
      // This is the @ViewsFilter() annotation value of our plug-in.
      'id'      => 'omnipedia_attached_data_type',
    ];

    $data['omnipedia_attached_data']['date_range__value']['group'] = $groupName;

    $data['omnipedia_attached_data']['date_range__value']['filter'] = [
      'title'   => $this->t('Start date'),
      // This is the help text shown on the plug-in options modal in the Views
      // UI.
      'help'    => $this->t('Filter by attached data start date.'),
      // This is the column in the table that this plug-in operates on.
      'field'   => 'date_range__value',
      // This is the @ViewsFilter() annotation value of our plug-in.
      'id'      => 'omnipedia_date_range_start',
    ];

    $data['omnipedia_attached_data']['date_range__end_value']['group'] =
      $groupName;

    $data['omnipedia_attached_data']['date_range__end_value']['filter'] = [
      'title'   => $this->t('End date'),
      // This is the help text shown on the plug-in options modal in the Views
      // UI.
      'help'    => $this->t('Filter by attached data end date.'),
      // This is the column in the table that this plug-in operates on.
      'field'   => 'date_range__end_value',
      // This is the @ViewsFilter() annotation value of our plug-in.
      'id'      => 'omnipedia_date_range_end',
    ];

    return $data;
  }

}
