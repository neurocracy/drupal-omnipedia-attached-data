<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\omnipedia_date\Service\TimelineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the OmnipediaAttachedDataDateRange constraint.
 */
class OmnipediaAttachedDataDateRangeValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * Constructor; saves dependencies.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The Drupal entity type plug-in manager.
   *
   * @param \Drupal\omnipedia_date\Service\TimelineInterface $timeline
   *   The Omnipedia timeline service.
   */
  public function __construct(
    protected readonly EntityTypeManagerInterface $entityTypeManager,
    protected readonly TimelineInterface $timeline,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('omnipedia.timeline'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {

    /** @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface */
    $entity = $value->getEntity();

    /** @var \Drupal\Core\Entity\EntityStorageInterface The Omnipedia attached data entity storage. */
    $storage = $this->entityTypeManager->getStorage('omnipedia_attached_data');

    /** @var string[] Zero or more attached entity IDs, keyed by their most recent revision ID. */
    $queryResult = ($storage->getQuery())
      ->condition('type',   $entity->type->value)
      ->condition('target', $entity->target->value)
      // Exclude the entity being validated, as it'll always overlap with
      // itself.
      ->condition('id', $entity->id(), '<>')
      ->accessCheck(true)
      ->execute();

    /** @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface[] */
    $otherEntities = $storage->loadMultiple($queryResult);

    // If there are no other attached data with the same target, the date
    // range is considered valid.
    if (empty($otherEntities)) {
      return;
    }

    foreach ($value as $delta => $item) {

      /** @var string  */
      $startDate = $entity->getStartDate();

      /** @var string  */
      $endDate = $entity->getEndDate();

      foreach ($otherEntities as $otherEntityId => $otherEntity) {

        /** @var string  */
        $otherStartDate = $otherEntity->getStartDate();

        /** @var string  */
        $otherEndDate = $otherEntity->getEndDate();

        if (!$this->timeline->doDateRangesOverlap(
          $startDate, $endDate, $otherStartDate, $otherEndDate, true
        )) {
          continue;
        }

        $this->context->addViolation(
          $constraint->message, [
            '%entityLabel'  => $otherEntity->label(),
            ':entityUrl'    => $otherEntity->toUrl(),
            '%startDate'    => $this->timeline->getDateFormatted(
              $otherStartDate, 'short'
            ),
            '%endDate'      => $this->timeline->getDateFormatted(
              $otherEndDate, 'short'
            ),
          ]
        );

      }

    }

  }

}
