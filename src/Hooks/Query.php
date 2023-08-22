<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Hooks;

use Drupal\Core\Database\Query\AlterableInterface;
use Drupal\hux\Attribute\Alter;

/**
 * Query hook implementations.
 */
class Query {

  #[Alter('query')]
  /**
   * Implements \hook_query_alter().
   *
   * @param \Drupal\Core\Database\Query\AlterableInterface $query
   *
   * This limits entity queries tagged with
   * 'non_overlapping_entity_date_range_validate' to only search for attached
   * data entities that also have the same type and target values as the one
   * being validated.
   *
   * @see \hook_query_alter()
   */
  public function nonOverlappingDateRangeAlter(
    AlterableInterface $query,
  ): void {

    if (!$query->hasTag('non_overlapping_entity_date_range_validate')) {
      return;
    }

    /** @var \Drupal\omnipedia_date\Entity\EntityWithDateRangeInterface */
    $entity = $query->getMetaData('entity_validate');

    if ($entity->getEntityTypeId() !== 'omnipedia_attached_data') {
      return;
    }

    // Note that we need to prefix the fields with the 'base_table' alias to
    // avoid potential fatal errors like this:
    //
    // "Drupal\Core\Database\IntegrityConstraintViolationException: SQLSTATE[23000]: Integrity constraint violation: 1052 Column 'type' in where clause is ambiguous"
    $query
      ->condition('base_table.type',   $entity->type->value)
      ->condition('base_table.target', $entity->target->value);

  }

}
