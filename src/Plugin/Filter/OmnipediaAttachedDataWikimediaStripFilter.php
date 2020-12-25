<?php

namespace Drupal\omnipedia_attached_data\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface;
use Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\WikimediaLink;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a filter to strip Wikimedia attached data link href attributes.
 *
 * @Filter(
 *   id           = "omnipedia_attached_data_wikimedia_strip",
 *   title        = @Translation("Omnipedia: Strip Wikimedia attached data link href attributes"),
 *   description  = @Translation("This strips href attributes from Wikimedia links that have attached data. This should be placed <strong>after</strong> the Markdown filter in the processing order."),
 *   type         = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE
 * )
 */
class OmnipediaAttachedDataWikimediaStripFilter extends FilterBase {

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

    /** @var \Symfony\Component\DomCrawler\Crawler */
    $linkCrawler = $rootCrawler->filter(
      'a[' . WikimediaLink::getIsWikimediaLinkAttributeName() . ']'
    );

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
