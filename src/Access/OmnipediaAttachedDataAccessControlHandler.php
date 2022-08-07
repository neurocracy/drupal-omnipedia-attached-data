<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the OmnipediaAttachedData entity.
 */
class OmnipediaAttachedDataAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(
    EntityInterface $entity, $operation, AccountInterface $account
  ) {
    $adminPermission = $this->entityType->getAdminPermission();

    if ($account->hasPermission($adminPermission)) {
      return AccessResult::allowed();
    }

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission(
          $account, 'view omnipedia_attached_data entity'
        );

      case 'update':
        return AccessResult::allowedIfHasPermission(
          $account, 'edit omnipedia_attached_data entity'
        );

      case 'delete':
        return AccessResult::allowedIfHasPermission(
          $account, 'delete omnipedia_attached_data entity'
        );
    }

    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   *
   * Separate from the checkAccess because the entity does not yet exist. It
   * will be created during the 'add' process.
   */
  protected function checkCreateAccess(
    AccountInterface $account, array $context, $entityBundle = null
  ) {
    $adminPermission = $this->entityType->getAdminPermission();

    // Admin permission overrides all others.
    if ($account->hasPermission($adminPermission)) {
      return AccessResult::allowed();
    }

    return AccessResult::allowedIfHasPermission(
      $account, 'add omnipedia_attached_data entity'
    );
  }

}
