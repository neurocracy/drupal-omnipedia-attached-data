<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Plugin\views\field;

use Drupal\views\Plugin\views\field\BulkForm;

/**
 * Defines an Omnipedia attached data operations bulk form element.
 *
 * @ViewsField("omnipedia_attached_data_bulk_form")
 */
class OmnipediaAttachedDataBulkForm extends BulkForm {

  /**
   * {@inheritdoc}
   */
  protected function emptySelectedMessage() {
    return $this->t('No attached data selected.');
  }

}
