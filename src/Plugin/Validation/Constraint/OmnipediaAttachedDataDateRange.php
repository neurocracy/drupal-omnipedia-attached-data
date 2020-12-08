<?php

namespace Drupal\omnipedia_attached_data\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the Omnipedia attached data date range doesn't overlap another.
 *
 * This specifically checks that attached data with the same type and target
 * cannot overlap their date ranges.
 *
 * @Constraint(
 *   id     = "OmnipediaAttachedDataDateRange",
 *   label  = @Translation("Omnipedia attached data date range", context = "Validation"),
 *   type   = "string"
 * )
 */
class OmnipediaAttachedDataDateRange extends Constraint {

  /**
   * The default violation message.
   *
   * @var string
   */
  public $message = 'The date range overlaps with <a href="@entityUrl">%entityLabel</a> (%startDate to %endDate).';

}
