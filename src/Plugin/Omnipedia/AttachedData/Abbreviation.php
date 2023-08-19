<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData;

use Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\OmnipediaAttachedDataBase;
use Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\OmnipediaAttachedDataInterface;
use Drupal\omnipedia_content\Service\AbbreviationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abbreviation attached data plug-in.
 *
 * @OmnipediaAttachedData(
 *   id           = "abbreviation",
 *   title        = @Translation("Abbreviation"),
 *   description  = @Translation("An abbreviated term to match, e.g. <abbr title='HyperText Markup Language'>HTML</abbr>, <abbr title='Cascading Style Sheets'>CSS</abbr>, etc.")
 * )
 */
class Abbreviation extends OmnipediaAttachedDataBase {

  /**
   * The Omnipedia abbreviation service.
   *
   * @var \Drupal\omnipedia_content\Service\AbbreviationInterface
   */
  protected AbbreviationInterface $abbreviation;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration, $pluginId, $pluginDefinition
  ) {

    $instance = parent::create(
      $container, $configuration, $pluginId, $pluginDefinition
    );

    $instance->setAbbreviationService(
      $container->get('omnipedia.abbreviation')

    );

    return $instance;

  }

  /**
   * Set the Omnipedia abbreviation service dependency.
   *
   * @param \Drupal\omnipedia_content\Service\AbbreviationInterface $abbreviation
   *   The Omnipedia abbreviation service.
   *
   * @return $this
   *   The plug-in instance for chaining.
   */
  public function setAbbreviationService(
    AbbreviationInterface $abbreviation
  ): OmnipediaAttachedDataInterface {

    $this->abbreviation = $abbreviation;

    return $this;

  }

  /**
   * Get all defined abbreviations.
   *
   * Note that abbreviation descriptions are assumed to not contain any Markdown
   * or HTML, as it will not be rendered. We don't currently need any rendering,
   * and attempting to do so here will result in a huge performance hit and
   * likely running out of memory.
   *
   * @param string|null|\Drupal\Core\Datetime\DrupalDateTime $date
   *   The date that the abbreviations are intended for. This is to ensure
   *   abbreviations vary correctly by start and end dates.
   *
   * @return string[]
   *   An array of abbreviations; keys are the abbreviated term and values are
   *   the description of that term.
   *
   * @todo Rework this so that we don't have to load literally all abbreviation
   *   type attached data entities.
   *
   * @see \Drupal\omnipedia_core\Service\WikiNodeTracker
   *   Implement a system to store information about attached data in a similar
   *   manner as the wiki node tracker service, via Drupal's state system.
   */
  public function getAllAbbreviations($date): array {

    $abbreviations = [];

    /** @var \Drupal\Core\Entity\EntityStorageInterface The Omnipedia attached data entity storage. */
    $storage = $this->entityTypeManager->getStorage('omnipedia_attached_data');

    /** @var string[] Zero or more attached entity IDs, keyed by their most recent revision ID. */
    $queryResult = ($storage->getQuery())
      ->condition('type', 'abbreviation')
      ->accessCheck(true)
      ->execute();

    foreach ($queryResult as $revisionId => $id) {

      /** @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface */
      $entity = $storage->load($id);

      // Ignore this attached data entity if its date range does not fall within
      // the provided date. Note that we include unpublished dates so that this
      // can apply to unpublished content that has no published content for that
      // date.
      if (!$this->timeline->isDateBetween(
        $date, $entity->getStartDate(), $entity->getEndDate(), true
      )) {
        continue;
      }

      /** @var string */
      $abbreviations[$entity->target->value] = $entity->content->value;

    }

    return $abbreviations;

  }

}
