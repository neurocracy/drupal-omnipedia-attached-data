<?php

namespace Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\omnipedia_attached_data\OmnipediaAttachedDataBase;
use Drupal\omnipedia_content\Service\WikimediaLinkInterface;
use Drupal\omnipedia_core\Service\TimelineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Wikimedia link attached data plug-in.
 *
 * @OmnipediaAttachedData(
 *   id           = "wikimedia_link",
 *   title        = @Translation("Wikimedia link"),
 *   description  = @Translation("A Wikimedia prefixed link pointing to an article on one of the Wikimedia sites, e.g. <code>wikipedia:Owl</code> will point to the Wikipedia article about Owls.")
 * )
 */
class WikimediaLink extends OmnipediaAttachedDataBase {

  /**
   * The Omnipedia Wikimedia link service.
   *
   * @var \Drupal\omnipedia_content\Service\WikimediaLinkInterface
   */
  protected $wikimediaLink;

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\omnipedia_content\Service\WikimediaLinkInterface $wikimediaLink
   *   The Omnipedia Wikimedia link service.
   */
  public function __construct(
    array $configuration, string $pluginId, array $pluginDefinition,
    RendererInterface           $renderer,
    TranslationInterface        $stringTranslation,
    TimelineInterface           $timeline,
    EntityTypeManagerInterface  $entityTypeManager,
    WikimediaLinkInterface      $wikimediaLink
  ) {
    parent::__construct(
      $configuration, $pluginId, $pluginDefinition,
      $entityTypeManager, $renderer, $stringTranslation, $timeline
    );

    // Save dependencies.
    $this->wikimediaLink      = $wikimediaLink;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration, $pluginId, $pluginDefinition
  ) {
    return new static(
      $configuration, $pluginId, $pluginDefinition,
      $container->get('renderer'),
      $container->get('string_translation'),
      $container->get('omnipedia.timeline'),
      $container->get('entity_type.manager'),
      $container->get('omnipedia.wikimedia_link')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @todo List valid prefixes?
   */
  public function validateTarget(string $target): array {
    $errors = [];

    if (!$this->wikimediaLink->isPrefixUrl($target)) {
      $errors[] = $this->t(
        '"@target" does not begin with a valid Wikimedia link prefix.',
        ['@target' => $target]
      );
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterTarget(string $target, $date): string {
    // CommonMark requires link URLs to not contain spaces, so content is
    // authored with underscores, but attached data is authored with spaces, so
    // we replace underscores with spaces here.
    return \str_replace('_', ' ', $target);
  }

}
