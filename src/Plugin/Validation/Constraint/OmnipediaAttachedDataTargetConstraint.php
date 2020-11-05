<?php

namespace Drupal\omnipedia_attached_data\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted Omnipedia attached data target value is valid.
 *
 * @Constraint(
 *   id     = "OmnipediaAttachedDataTarget",
 *   label  = @Translation("Omnipedia attached data target", context = "Validation"),
 *   type   = "string"
 * )
 */
class OmnipediaAttachedDataTargetConstraint extends Constraint {

  /**
   * The default violation message.
   *
   * @var string
   */
  public $message = '%error';

}
