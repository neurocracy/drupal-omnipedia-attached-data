<?php

namespace Drupal\omnipedia_attached_data\Plugin\Filter;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter to add attached data attachments (libraries and settings).
 *
 * @Filter(
 *   id           = "omnipedia_attached_data_attachments",
 *   title        = @Translation("Omnipedia: Attached data libraries and settings"),
 *   description  = @Translation("This attaches any libraries and JavaScript settings to the filter results that are provided by attached data plug-ins. This should be placed <strong>after</strong> the Markdown filter and any other Omnipedia attached data filters in the processing order."),
 *   type         = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 *
 * @todo Can we instead access the Markdown FilterProcessResult so that
 *   attachments can be added there without the need for this additional filter?
 *
 * @see \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface::getAttachments()
 *   Called to retrieve any attachments from available plug-ins.
 */
class OmnipediaAttachedDataAttachmentsFilter extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The OmnipediaAttachedData plug-in manager.
   *
   * @var \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface
   */
  protected $attachedDataManager;

  /**
   * Constructs this filter object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plug-in instance.
   *
   * @param string $pluginID
   *   The plugin_id for the plug-in instance.
   *
   * @param array $pluginDefinition
   *   The plug-in implementation definition. PluginBase defines this as mixed,
   *   but we should always have an array so the type is set.
   *
   * @param \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface $attachedDataManager
   *   The OmnipediaAttachedData plug-in manager.
   */
  public function __construct(
    array $configuration, string $pluginID, array $pluginDefinition,
    OmnipediaAttachedDataManagerInterface $attachedDataManager
  ) {
    parent::__construct($configuration, $pluginID, $pluginDefinition);

    // Save dependencies.
    $this->attachedDataManager = $attachedDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration, $pluginID, $pluginDefinition
  ) {
    return new static(
      $configuration, $pluginID, $pluginDefinition,
      $container->get('plugin.manager.omnipedia_attached_data')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface::getAttachments()
   *   Retrieves any attachements defined by plug-ins.
   */
  public function process($text, $langCode) {
    /** @var \Drupal\filter\FilterProcessResult */
    $result = new FilterProcessResult($text);

    $result->addAttachments($this->attachedDataManager->getAttachments());

    return $result;
  }

}
