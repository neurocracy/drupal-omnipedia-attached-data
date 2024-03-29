<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\EventSubscriber\Omnipedia;

use Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface;
use Drupal\omnipedia_content\Event\Omnipedia\AbbreviationsBuildEvent;
use Drupal\omnipedia_content\Event\Omnipedia\OmnipediaContentEventInterface;
use Drupal\omnipedia_date\Service\TimelineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber to register attached data abbrevations.
 */
class AbbreviationsBuildEventSubscriber implements EventSubscriberInterface{

  /**
   * Event subscriber constructor; saves dependencies.
   *
   * @param \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface $attachedDataManager
   *   The OmnipediaAttachedData plug-in manager.
   *
   * @param \Drupal\omnipedia_date\Service\TimelineInterface $timeline
   *   The Omnipedia timeline service.
   */
  public function __construct(
    protected readonly OmnipediaAttachedDataManagerInterface  $attachedDataManager,
    protected readonly TimelineInterface                      $timeline,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      OmnipediaContentEventInterface::ABBREVIATIONS_BUILD =>
        'onAbbreviationsBuild',
    ];
  }

  /**
   * AbbreviationsBuildEvent callback.
   *
   * @param \Drupal\omnipedia_content\Event\Omnipedia\AbbreviationsBuildEvent $event
   *   The event object.
   *
   * @todo Find way of getting the date of the content we're rendering in, so
   *   that we don't have to use 'current' as the date.
   */
  public function onAbbreviationsBuild(AbbreviationsBuildEvent $event): void {

    /** @var \Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\OmnipediaAttachedDataInterface */
    $instance = $this->attachedDataManager->createInstance(
      'abbreviation', []
    );

    $event->addAbbreviations($instance->getAllAbbreviations(
      $this->timeline->getDateFormatted('current', 'storage')
    ));

  }

}
