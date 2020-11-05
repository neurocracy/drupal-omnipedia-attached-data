<?php

namespace Drupal\omnipedia_attached_data;

use Drupal\Core\Form\FormStateInterface;

/**
 * An interface for all OmnipediaAttachedData plug-ins.
 */
interface OmnipediaAttachedDataInterface {

  /**
   * Validate a given target string and return any error messages.
   *
   * @param string $target
   *   The target string to validate.
   *
   * @return array
   *   An array of zero or more localized error messages. If the array is empty,
   *   no validation errors were reported.
   */
  public function validateTarget(string $target): array;

}
