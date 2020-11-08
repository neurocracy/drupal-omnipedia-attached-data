<?php

namespace Drupal\omnipedia_attached_data\Plugin\Filter;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a filter to strip Wikimedia attached data link href attributes.
 *
 * @Filter(
 *   id = "omnipedia_attached_data_wikimedia_strip",
 *   title = @Translation("Omnipedia: Strip Wikimedia attached data link href attributes"),
 *   description = @Translation("This strips href attributes from Wikimedia links that have attached data. This should be placed <strong>after</strong> the Markdown filter in the processing order."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE
 * )
 *
 * @todo Can most of this functionality be moved to the Wikimedia attached data
 *   plug-in and abstracted to be useful to other plug-ins?
 */
class OmnipediaAttachedDataWikimediaStripFilter extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The OmnipediaAttachedData plug-in manager.
   *
   * @var \Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface
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
   * @param \Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface $attachedDataManager
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
   */
  public function process($text, $langCode) {
    /** @var \Symfony\Component\DomCrawler\Crawler */
    $rootCrawler = new Crawler(
      // The <div> is to prevent the PHP DOM automatically wrapping any
      // top-level text content in a <p> element.
      '<div id="omnipedia-attached-data-wikimedia-strip-filter-root">' .
        $text .
      '</div>'
    );

    $linkCrawler = $rootCrawler->filter('a[' .
      $this->attachedDataManager->getAttachedDataAttributeName() .
    ']');

    foreach ($linkCrawler as $link) {
      $link->removeAttribute('href');
    }

    return new FilterProcessResult(
      $rootCrawler->filter(
        '#omnipedia-attached-data-wikimedia-strip-filter-root'
      )->html()
    );
  }

}
