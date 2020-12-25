<?php

namespace Drupal\omnipedia_attached_data\EventSubscriber\Preprocess;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;
use Drupal\omnipedia_core\Service\WikiNodeResolverInterface;
use Drupal\preprocess_event_dispatcher\Event\FieldPreprocessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber to add attached data cache tags to appropriate fields.
 *
 * Note that we can't predict if new attached data has been added that may
 * match the content of this field without parsing the field contents and
 * keeping some sort of index in a searchable form. While that may be
 * necessary if this system ever needs to scale to a really high traffic site,
 * the performance hit of rebuilding the field is likely not going to be
 * significant.
 */
class AttachedDataCacheTagsPreprocessFieldEventSubscriber implements EventSubscriberInterface {

  /**
   * The Drupal entity type plug-in manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Omnipedia wiki node resolver service.
   *
   * @var \Drupal\omnipedia_core\Service\WikiNodeResolverInterface
   */
  protected $wikiNodeResolver;

  /**
   * Event subscriber constructor; saves dependencies.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The Drupal entity type plug-in manager.
   *
   * @param \Drupal\omnipedia_core\Service\WikiNodeResolverInterface $wikiNodeResolver
   *   The Omnipedia wiki node resolver service.
   */
  public function __construct(
    EntityTypeManagerInterface  $entityTypeManager,
    WikiNodeResolverInterface   $wikiNodeResolver
  ) {
    $this->entityTypeManager  = $entityTypeManager;
    $this->wikiNodeResolver   = $wikiNodeResolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      FieldPreprocessEvent::name() => 'onPreprocessField',
    ];
  }

  /**
   * Add attached data cache tags to the body field on wiki nodes.
   *
   * @param \Drupal\preprocess_event_dispatcher\Event\FieldPreprocessEvent $event
   *   The event object.
   *
   * @todo Can this instead be applied to all fields that contain a
   *   "#type" => "processed_text" with a text format that has the Markdown
   *   filter? Would that be overkill?
   */
  public function onPreprocessField(FieldPreprocessEvent $event): void {
    /** @var \Drupal\preprocess_event_dispatcher\Variables\FieldEventVariables */
    $variables = $event->getVariables();

    // Bail if this isn't a field with the name 'body'.
    if ($variables->get('field_name') !== 'body') {
      return;
    }

    /** @var array */
    $element = $variables->getElement();

    // Bail if this is not a wiki node.
    if (
      empty($element['#object']) ||
      !$this->wikiNodeResolver->isWikiNode($element['#object'])
    ) {
      return;
    }

    /** @var array */
    $cache = $variables->get('#cache', []);

    // Get the entity type definition so that we can get the list cache tag.
    // This is considered a best practice over hard coding the tag.
    /** @var \Drupal\Core\Entity\EntityTypeInterface|null */
    $entityType = $this->entityTypeManager->getDefinition(
      'omnipedia_attached_data'
    );

    // Bail if the entity was not found for whatever reason.
    if (!($entityType instanceof EntityTypeInterface)) {
      return;
    }

    // Merge tags if some already exist.
    if (isset($cache['tags'])) {
      $cache['tags'] = Cache::mergeTags(
        $cache['tags'],
        $entityType->getListCacheTags()
      );

    } else {
      $cache['tags'] = $entityType->getListCacheTags();
    }

    $variables->set('#cache', $cache);
  }

}
