<?php

namespace Drupal\omnipedia_attached_data;

/**
 * Defines an interface for OmnipediaAttachedData plug-in managers.
 */
interface OmnipediaAttachedDataManagerInterface {

  /**
   * Get allowed option values for the attached data 'type' field.
   *
   * @return array
   *   An array of allowed options based on the discovered plug-ins.
   *
   * @see \callback_allowed_values_function()
   *   Describes expected format.
   */
  public function getAttachedDataTypeOptionValues(): array;

  /**
   * Get the default value for the attached data 'type' field.
   *
   * @return string|null
   *   The machine name of the default attached data plug-in.
   */
  public function getAttachedDataTypeDefaultValue(): ?string;

  /**
   * Get a user-presentable type plug-in label.
   *
   * @param string $machineName
   *   The machine name of the plug-in to return the label of.
   *
   * @return string
   *   The label for the requested plug-in machine name, or an empty string if
   *   the plug-in doesn't exist.
   */
  public function getAttachedDataTypeLabel(string $machineName): string;

  /**
   * Validate a given target string and return any error messages.
   *
   * @param string $pluginId
   *   The OmnipediaAttachedData plug-in machine name to validate with.
   *
   * @param string $target
   *   The target string to validate.
   *
   * @return array
   *   An array of zero or more localized error messages. If the array is empty,
   *   no validation errors were reported.
   */
  public function validateTarget(string $pluginId, string $target): array;

  /**
   * Get content matching the given plug-in ID and target string.
   *
   * @param string $pluginId
   *   The OmnipediaAttachedData plug-in machine name to get content for.
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
  public function getContent(
    string $pluginId, string $target, $date = null
  ): ?string;

  /**
   * Get the attached data content HTML attribute name.
   *
   * @return string
   *   The attached data content HTML attribute name.
   */
  public function getAttachedDataContentAttributeName(): string;

}
