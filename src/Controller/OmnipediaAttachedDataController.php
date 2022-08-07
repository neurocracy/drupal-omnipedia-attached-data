<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface;

/**
 * Returns responses for OmnipediaAttachedData entity routes.
 */
class OmnipediaAttachedDataController extends ControllerBase {

  /**
   * Attached data route title callback.
   *
   * @param \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface $omnipedia_attached_data
   *   The OmnipediaAttachedData entity to return the title of. Note that
   *   Symfony requires the parameter name to match the
   *   '{omnipedia_attached_data}' in the route path.
   *
   * @return array
   *   The title of the provided OmnipediaAttachedData entity as a render array.
   */
  public function getAttachedDataTitle(
    OmnipediaAttachedDataInterface $omnipedia_attached_data
  ): array {

    return [
      '#markup'       => $omnipedia_attached_data->getTitle(),
      '#allowed_tags' => Xss::getHtmlTagList(),
    ];

  }

}
