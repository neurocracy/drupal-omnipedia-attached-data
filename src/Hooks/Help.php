<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Hooks;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\hux\Attribute\Hook;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Help hook implementations.
 */
class Help implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * Hook constructor; saves dependencies.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   *   The Drupal string translation service.
   */
  public function __construct(protected $stringTranslation) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('string_translation'),
    );
  }

  #[Hook('help')]
  /**
   * Implements \hook_help().
   *
   * @param string $routeName
   *   The current route name.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   *
   * @return \Drupal\Component\Render\MarkupInterface|array|string
   */
  public function help(
    string $routeName, RouteMatchInterface $routeMatch,
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
