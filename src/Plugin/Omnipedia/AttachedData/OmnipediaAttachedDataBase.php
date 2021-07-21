<?php

namespace Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\OmnipediaAttachedDataInterface;
use Drupal\omnipedia_core\Service\TimelineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for implementing OmnipediaAttachedData plug-ins.
 */
abstract class OmnipediaAttachedDataBase extends PluginBase implements ContainerFactoryPluginInterface, OmnipediaAttachedDataInterface {

  use StringTranslationTrait;

  /**
   * The Drupal entity type plug-in manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Drupal renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The Omnipedia timeline service.
   *
   * @var \Drupal\omnipedia_core\Service\TimelineInterface
   */
  protected $timeline;

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
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The Drupal entity type plug-in manager.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The Drupal renderer service.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   *   The Drupal string translation service.
   *
   * @param \Drupal\omnipedia_core\Service\TimelineInterface $timeline
   *   The Omnipedia timeline service.
   */
  public function __construct(
    array $configuration, string $pluginId, array $pluginDefinition,
    EntityTypeManagerInterface  $entityTypeManager,
    RendererInterface           $renderer,
    TranslationInterface        $stringTranslation,
    TimelineInterface           $timeline
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);

    // Save dependencies.
    $this->entityTypeManager  = $entityTypeManager;
    $this->renderer           = $renderer;
    $this->stringTranslation  = $stringTranslation;
    $this->timeline           = $timeline;
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
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('string_translation'),
      $container->get('omnipedia.timeline')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @todo Make this default to the plug-in's description annotation.
   */
  public function getSummaryItem(): string {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getGuidelines(): string {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function validateTarget(string $target): array {
    return [];
  }

  /**
   * Alter the target string before it's used to match attached data.
   *
   * Override this method to perform any alterations needed.
   *
   * @param string $target
   *   The target string.
   *
   * @param string|null|\Drupal\Core\Datetime\DrupalDateTime $date
   *   The date that the content is intended for.
   *
   * @return string
   *   The target string with any alterations.
   *
   * @todo Should this method have a more specific name? What about for
   *   validateTarget(), etc.?
   */
  protected function alterTarget(string $target, $date): string {
    return $target;
  }

  /**
   * {@inheritdoc}
   *
   * @todo Can we instead return a render array rather than rendering here?
   */
  public function getContent(string $target, $date = null): ?string {
    // Check that the $target parameter passes validation and return here if it
    // doesn't.
    if (count($this->validateTarget($target)) > 0) {
      return null;
    }

    /** @var \Drupal\Core\Entity\EntityStorageInterface */
    $storage = $this->entityTypeManager->getStorage('omnipedia_attached_data');

    /** @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface[] */
    $entities = $storage->loadByProperties([
      'target' => $this->alterTarget($target, $date),
    ]);

    /** @var string|null */
    $markup = null;

    // Loop through all found attached data entities, looking for one that
    // matches our criteria.
    foreach ($entities as $id => $entity) {
      // Ignore this attached data entity if its date range does not fall within
      // the provided date. Note that we include unpublished dates so that this
      // can apply to unpublished content that has no published content for that
      // date.
      if (!$this->timeline->isDateBetween(
        $date, $entity->getStartDate(), $entity->getEndDate(), true
      )) {
        continue;
      }

      /** @var array */
      $renderArray = $entity->content->view();

      /** @var string */
      $markup = (string) $this->renderer->render($renderArray[0]);

      // Break out of the loop once we've found and rendered a matching attached
      // data entity.
      break;
    }

    return $markup;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachements(): array {
    return [];
  }

}
