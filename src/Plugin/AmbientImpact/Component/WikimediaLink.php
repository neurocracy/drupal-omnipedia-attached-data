<?php

namespace Drupal\omnipedia_attached_data\Plugin\AmbientImpact\Component;

use Drupal\ambientimpact_core\ComponentBase;
use Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\WikimediaLink as WikimediaLinkAttachedData;

/**
 * Wikimedia link component.
 *
 * @Component(
 *   id = "wikimedia_link",
 *   title = @Translation("Wikimedia link"),
 *   description = @Translation("Provides content pop-ups for Wikimedia links that have data attached.")
 * )
 */
class WikimediaLink extends ComponentBase {

  /**
   * {@inheritdoc}
   *
   * @todo Determine why this isn't being made available to the JavaScript
   *   component in this.settings despite being present in drupalSettings.
   */
  public function getJSSettings(): array {
    return [
      'isWikimediaLinkAttributeName' =>
        WikimediaLinkAttachedData::getIsWikimediaLinkAttributeName()
    ];
  }

}
