<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Omnipedia attached data migration source plug-in.
 *
 * @MigrateSource(
 *   id             = "omnipedia_attached_data",
 *   source_module  = "omnipedia_attached_data",
 * )
 */
class OmnipediaAttachedData extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('omnipedia_attached_data', 'oad')
      ->fields('oad', [
        'id',
        'type',
        'target',
        'content',
        'content_format',
        'language',
        'created',
        'date_start',
        'date_end',
      ]);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id'      => $this->t('Primary key of this entity'),
      'type'    => $this->t('The type of attached data'),
      'target'  => $this->t('Identifies the target to attach data to'),
      'content'         => $this->t('The text content of this entity'),
      'content_format'  => $this->t('The input format for the text content of this entity'),
      'language'  => $this->t('The language of this entity'),
      'created'   => $this->t('The Unix timestamp of the entity creation time'),
      'date_start'  => $this->t('The earliest date this attached data is to be displayed on.'),
      'date_end'    => $this->t('The latest date this attached data is to be displayed on.'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type'  => 'integer',
        'alias' => 'oad',
      ],
    ];
  }

}
