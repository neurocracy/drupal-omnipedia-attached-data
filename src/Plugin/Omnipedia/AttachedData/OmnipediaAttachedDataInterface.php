<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData;

use Drupal\Core\Form\FormStateInterface;

/**
 * An interface for all OmnipediaAttachedData plug-ins.
 */
interface OmnipediaAttachedDataInterface {

  /**
   * Get a brief summary of what this plug-in does.
   *
   * @return string
   *   A short, single sentence translated string.
   *
   * @see \Drupal\filter\Plugin\FilterInterface::tips()
   *   Equivalent of when $long === false for filter tips.
   */
  public function getSummaryItem(): string;

  /**
   * Get detailed guidelines of how to use this plug-in.
   *
   * @return string
   *   Translated guidelines as a translated string.
   *
   * @see \Drupal\filter\Plugin\FilterInterface::tips()
   *   Equivalent of when $long === true for filter tips.
   *
   * @todo Should this allow returning render arrays?
   */
  public function getGuidelines(): string;

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

  /**
   * Get attachments (libraries and settings) for this plug-in.
   *
   * @return array
   *   An array of attachments.
   *
   * @see https://www.drupal.org/docs/creating-custom-modules/adding-stylesheets-css-and-javascript-js-to-a-drupal-module
   */
  public function getAttachements(): array;

}
