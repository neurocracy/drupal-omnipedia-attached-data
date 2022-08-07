<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\omnipedia_date\Service\TimelineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for the OmnipediaAttachedData entity.
 */
class OmnipediaAttachedDataListBuilder extends EntityListBuilder {

  /**
   * The Omnipedia timeline service.
   *
   * @var \Drupal\omnipedia_date\Service\TimelineInterface
   */
  protected TimelineInterface $timeline;

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\omnipedia_date\Service\TimelineInterface $timeline
   *   The Omnipedia timeline service.
   */
  public function __construct(
    EntityTypeInterface     $entityType,
    EntityStorageInterface  $storage,
    TimelineInterface       $timeline
  ) {
    parent::__construct($entityType, $storage);

    // Save dependencies.
    $this->timeline = $timeline;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(
    ContainerInterface $container, EntityTypeInterface $entityType
  ) {
    return new static(
      $entityType,
      $container->get('entity_type.manager')->getStorage($entityType->id()),
      $container->get('omnipedia.timeline')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['target'] = $this->t('Target');
    $header['type']   = $this->t('Type');

    $header['date_start'] = $this->t('Start date');
    $header['date_end']   = $this->t('End date');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var string */
    $row['target']  = $entity->toLink($entity->getTitle())->toString();

    /** @var string */
    $row['type']    = $entity->getTypeLabel();

    /** @var string */
    $row['date_start'] = $this->timeline->getDateFormatted(
      $entity->getStartDate(), 'short'
    );

    /** @var string */
    $row['date_end'] = $this->timeline->getDateFormatted(
      $entity->getEndDate(), 'short'
    );

    return $row + parent::buildRow($entity);
  }

}
