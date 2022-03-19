<?php declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Service;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;
use Drupal\omnipedia_core\Service\HelpInterface;

/**
 * The Omnipedia attached data help service.
 */
class Help implements HelpInterface {

  use StringTranslationTrait;

  /**
   * Service constructor; saves dependencies.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   *   The Drupal string translation service.
   */
  public function __construct(TranslationInterface $stringTranslation) {
    $this->stringTranslation = $stringTranslation;
  }

  /**
   * {@inheritdoc}
   */
  public function help(
    string $routeName, RouteMatchInterface $routeMatch
  ): MarkupInterface|array|string {

    switch ($routeName) {
      case 'omnipedia_attached_data.configuration':

        return $this->getConfigurationHelp();

      case 'entity.omnipedia_attached_data.collection':

        return $this->getCollectionHelp();

    }

    return [];

  }

  /**
   * Get help content for the attached data configuration route.
   *
   * @return array
   *   A render array.
   */
  protected function getConfigurationHelp(): array {

    /** @var \Drupal\Core\Url */
    $collectionUrl = Url::fromRoute(
      'entity.omnipedia_attached_data.collection'
    );

    /** @var array */
    $renderArray = [
      '#type' => 'html_tag',
      '#tag'  => 'p',
    ];

    if ($collectionUrl->access() === true) {

      /** @var \Drupal\Core\Link */
      $collectionLink = new Link(
        $this->t('View attached data'), $collectionUrl
      );

      $renderArray['#value'] = $this->t(
        'This lists all attached data types. Changing the order here will be reflected when adding or editing attached data. The type at the top of the list is the default type when creating new attached data. @collectionLink.',
        [
          // Unfortunately, this needs to be rendered here or it'll cause a
          // fatal error when Drupal tries to pass it to \htmlspecialchars().
          '@collectionLink' => $collectionLink->toString(),
        ]
      );

    } else {

      $renderArray['#value'] = $this->t(
        'This lists all attached data types. Changing the order here will be reflected when adding or editing attached data. The type at the top of the list is the default type when creating new attached data.'
      );

    }

    return $renderArray;

  }

  /**
   * Get help content for the attached data collection route.
   *
   * @return array
   *   A render array.
   */
  protected function getCollectionHelp(): array {

    /** @var \Drupal\Core\Url */
    $configurationUrl = Url::fromRoute(
      'omnipedia_attached_data.configuration'
    );

    /** @var array */
    $renderArray = [
      '#type' => 'html_tag',
      '#tag'  => 'p',
    ];

    if ($configurationUrl->access() === true) {

      /** @var \Drupal\Core\Link */
      $configurationLink = new Link(
        $this->t('attached data configuration'), $configurationUrl
      );

      $renderArray['#value'] = $this->t(
        'This lists all attached data that has been authored. See also the @configurationLink.',
        [
          // Unfortunately, this needs to be rendered here or it'll cause a
          // fatal error when Drupal tries to pass it to \htmlspecialchars().
          '@configurationLink' => $configurationLink->toString(),
        ]
      );

    } else {

      $renderArray['#value'] = $this->t(
        'This lists all attached data that has been authored.'
      );

    }

    return $renderArray;

  }

}
