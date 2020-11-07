<?php

namespace Drupal\omnipedia_attached_data\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the OmnipediaAttachedDataTarget constraint.
 */
class OmnipediaAttachedDataTargetConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * The OmnipediaAttachedData plug-in manager.
   *
   * @var \Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface
   */
  protected $attachedDataManager;

  /**
   * Constructs an OmnipediaAttachedDataTargetConstraintValidator object.
   *
   * @param \Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface $attachedDataManager
   *   The OmnipediaAttachedData plug-in manager.
   */
  public function __construct(
    OmnipediaAttachedDataManagerInterface $attachedDataManager
  ) {
    // Save dependencies.
    $this->attachedDataManager = $attachedDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.omnipedia_attached_data')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /** @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface */
    $entity = $value->getEntity();

    // Get the selected attached data plug-in ID.
    /** @var string */
    $typePluginId = $entity->type->value;

    foreach ($value as $delta => $item) {
      // Have the plug-in manager validate the target with the appropriate
      // plug-in.
      $errors = $this->attachedDataManager->validateTarget(
        $typePluginId, $item->value
      );

      foreach ($errors as $key => $error) {
        $this->context->addViolation(
          $constraint->message, ['%error' => $error]
        );
      }
    }
  }

}