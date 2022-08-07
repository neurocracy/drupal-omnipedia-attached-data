<?php

namespace Drupal\omnipedia_attached_data\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a OmnipediaAttachedData entity.
 */
interface OmnipediaAttachedDataInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Set dynamic allowed values for the type field.
   *
   * @param \Drupal\field\Entity\FieldStorageConfig $definition
   *   The field definition.
   *
   * @param \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface|null $entity
   *   The attached data entity being created if applicable.
   *
   * @param bool &$cacheable
   *   Boolean indicating if the results are cacheable.
   *
   * @return array
   *   An array of possible key and value options.
   *
   * @see \callback_allowed_values_function()
   *
   * @see \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface::getAttachedDataTypeOptionValues()
   */
  public static function attachedDataTypeAllowedValuesCallback(
    FieldStorageDefinitionInterface $definition,
    OmnipediaAttachedDataInterface $entity = null,
    bool &$cacheable = true
  ): array;

  /**
   * Sets the default value for the type field.
   *
   * @param \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface $entity
   *   The attached data entity being created.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $definition
   *   The field definition.
   *
   * @return array
   *   An array of default value keys with each entry keyed with the 'value'
   *   key.
   *
   * @see \Drupal\Core\Field\FieldConfigBase::getDefaultValue()
   *
   * @see \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface::getAttachedDataTypeDefaultValue()
   */
  public static function attachedDataTypeDefaultValueCallback(
    OmnipediaAttachedDataInterface $entity,
    FieldDefinitionInterface $definition
  ): array;

  /**
   * Get this OmnipediaAttachedData entity's user-presentable type label.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   The type label.
   */
  public function getTypeLabel(): TranslatableMarkup|string;

  /**
   * Get this OmnipediaAttachedData entity's user-presentable title.
   *
   * @return string
   *   The entity title.
   */
  public function getTitle(): string;

  /**
   * Get this OmnipediaAttachedData entity's start date.
   *
   * @return string
   *   Either the stored date string or the string 'first' if not set.
   */
  public function getStartDate(): string;

  /**
   * Get this OmnipediaAttachedData entity's end date.
   *
   * @return string
   *   Either the stored date string or the string 'last' if not set.
   */
  public function getEndDate(): string;

}
