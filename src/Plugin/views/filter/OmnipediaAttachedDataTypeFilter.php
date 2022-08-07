<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Plugin\views\filter;

use Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filter to handle Omnipedia attached data types.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("omnipedia_attached_data_type")
 */
class OmnipediaAttachedDataTypeFilter extends InOperator {

  /**
   * The OmnipediaAttachedData plug-in manager.
   *
   * @var \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface
   */
  protected OmnipediaAttachedDataManagerInterface $attachedDataManager;

  /**
   * Constructor; saves dependencies.
   *
   * @param array $configuration
   *   A configuration array containing information about the plug-in instance.
   *
   * @param string $pluginId
   *   The plug-in ID for the plug-in instance.
   *
   * @param mixed $pluginDefinition
   *   The plug-in implementation definition.
   *
   * @param \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface $attachedDataManager
   *   The OmnipediaAttachedData plug-in manager.
   */
  public function __construct(
    array $configuration, $pluginId, $pluginDefinition,
    OmnipediaAttachedDataManagerInterface $attachedDataManager
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);

    $this->attachedDataManager = $attachedDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $pluginId,
    $pluginDefinition
  ) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('plugin.manager.omnipedia_attached_data')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function init(
    ViewExecutable $view, DisplayPluginBase $display, array &$options = null
  ) {
    parent::init($view, $display, $options);

    $this->definition['options callback'] = [
      $this->attachedDataManager, 'getAttachedDataTypeOptionValues'
    ];
  }

}
