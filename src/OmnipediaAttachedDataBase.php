<?php

namespace Drupal\omnipedia_attached_data;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\omnipedia_attached_data\OmnipediaAttachedDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for implementing OmnipediaAttachedData plug-ins.
 */
abstract class OmnipediaAttachedDataBase extends PluginBase implements ContainerFactoryPluginInterface, OmnipediaAttachedDataInterface {

  use StringTranslationTrait;

  /**
   * Constructs an OmnipediaAttachedDataBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plug-in instance.
   *
   * @param string $pluginId
   *   The plugin_id for the plug-in instance.
   *
   * @param array $pluginDefinition
   *   The plug-in implementation definition. PluginBase defines this as mixed,
   *   but we should always have an array so the type is specified.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   *   The Drupal string translation service.
   */
  public function __construct(
    array $configuration, string $pluginId, array $pluginDefinition,
    TranslationInterface $stringTranslation
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);

    // Save dependencies.
    $this->stringTranslation = $stringTranslation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration, $pluginId, $pluginDefinition
  ) {
    return new static(
      $configuration, $pluginId, $pluginDefinition,
      $container->get('string_translation')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validateTarget(string $target): array {
    return [];
  }

}
