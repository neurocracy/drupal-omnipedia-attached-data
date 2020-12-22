<?php

namespace Drupal\omnipedia_attached_data\EventSubscriber\Omnipedia;

use Drupal\Component\Utility\Html;
use Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface;
use Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\WikimediaLink;
use Drupal\omnipedia_content\Event\Omnipedia\WikimediaLinkBuildEvent;
use Drupal\omnipedia_content\OmnipediaContentEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber to attach data to matching Wikimedia links.
 */
class WikimediaLinkBuildEventSubscriber implements EventSubscriberInterface{

  /**
   * The OmnipediaAttachedData plug-in manager.
   *
   * @var \Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface
   */
  protected $attachedDataManager;

  /**
   * Event subscriber constructor; saves dependencies.
   *
   * @param \Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface $attachedDataManager
   *   The OmnipediaAttachedData plug-in manager.
   */
  public function __construct(
    OmnipediaAttachedDataManagerInterface $attachedDataManager
  ) {
    $this->attachedDataManager = $attachedDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      OmnipediaContentEventInterface::WIKIMEDIA_LINK_BUILD => 'onWikimediaLinkBuild',
    ];
  }

  /**
   * WikimediaLinkBuildEvent callback.
   *
   * @param \Drupal\omnipedia_content\Event\Omnipedia\WikimediaLinkBuildEvent $event
   *   The event object.
   *
   * @todo Find way of getting the date of the content we're rendering in, so
   *   that we don't have to use 'current' as the date passed to
   *   OmnipediaAttachedDataManagerInterface::getContent().
   */
  public function onWikimediaLinkBuild(WikimediaLinkBuildEvent $event): void {
    /** @var \League\CommonMark\Inline\Element\Link */
    $link = $event->getLink();

    /** @var string|null */
    $attachedDataContent = $this->attachedDataManager->getContent(
      'wikimedia_link', $event->getPrefixedUrl(), 'current'
    );

    // Flag to indicate this a Wikimedia link. Note that we add this attribute
    // even if don't find any matching attached data so the filter to strip
    // hrefs can still find and alter them.
    $link->data['attributes'][
      WikimediaLink::getIsWikimediaLinkAttributeName()
    ] = 'true';

    if (!empty($attachedDataContent)) {
      $link->data['attributes'][
        $this->attachedDataManager->getAttachedDataTitleAttributeName()
      ] = Html::escape($event->getArticleTitle());

      $link->data['attributes'][
        $this->attachedDataManager->getAttachedDataContentAttributeName()
      ] = Html::escape($attachedDataContent);

      // Save a copy of the attached data content with tags stripped to the
      // title attribute as a fallback if our JavaScript fails for whatever
      // reason.
      $link->data['attributes']['title'] =
        Html::escape(\strip_tags($attachedDataContent));
    }
  }

}
