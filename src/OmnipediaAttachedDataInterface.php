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

  /**
   * Get content matching the given target string.
   *
   * @param string $target
   *   The target string to get content for.
   *
   * @param string|null|\Drupal\Core\Datetime\DrupalDateTime $date
   *   The date that the content is intended for. This enables content to vary
   *   based on start and end dates.
   *
   * @return string|null
   *   The string content matching the $target parameter or null if it can't be
   *   found.
   */
  public function getContent(string $target, $date = null): ?string;

}
