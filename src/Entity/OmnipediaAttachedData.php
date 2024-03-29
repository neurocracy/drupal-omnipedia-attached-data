<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface;
use Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface;
use Drupal\omnipedia_date\Entity\EntityWithDateRangeTrait;
use Drupal\user\UserInterface;

/**
 * Defines the OmnipediaAttachedData entity.
 *
 * @ContentEntityType(
 *   id           = "omnipedia_attached_data",
 *   label        = @Translation("Omnipedia: Attached data"),
 *   label_collection = @Translation("Attached data"),
 *   label_singular   = @Translation("attached data"),
 *   label_plural     = @Translation("attached data"),
 *   label_count      = @PluralTranslation(
 *     singular = "@count attached data item",
 *     plural   = "@count attached data items",
 *   ),
 *   base_table   = "omnipedia_attached_data",
 *   entity_keys  = {
 *     "id"   = "id",
 *     "uuid" = "uuid",
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\omnipedia_attached_data\Entity\Controller\OmnipediaAttachedDataListBuilder",
 *     "form"         = {
 *       "default"  = "Drupal\omnipedia_attached_data\Form\OmnipediaAttachedDataForm",
 *       "delete"   = "Drupal\omnipedia_attached_data\Form\OmnipediaAttachedDataDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "access"       = "Drupal\omnipedia_attached_data\Access\OmnipediaAttachedDataAccessControlHandler",
 *     "views_data"   = "Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataViewsData",
 *   },
 *   links = {
 *     "canonical"    = "/omnipedia/attached-data/{omnipedia_attached_data}",
 *     "edit-form"    = "/omnipedia/attached-data/{omnipedia_attached_data}/edit",
 *     "delete-form"  = "/omnipedia/attached-data/{omnipedia_attached_data}/delete",
 *     "delete-multiple-form" = "/admin/content/attached-data/delete-multiple",
 *     "collection"   = "/admin/content/attached-data",
 *   },
 * )
 *
 * @todo Make revisionable.
 *
 * @todo Make publishable.
 *
 * @todo Remove calls to \Drupal as much as possible and decouple this entity
 *   from services, either by using wrapped entities via the Typed Entity module
 *   or by other means.
 */
class OmnipediaAttachedData extends ContentEntityBase implements OmnipediaAttachedDataInterface {

  use EntityChangedTrait;
  use EntityWithDateRangeTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entityType) {

    return [
      'id'  => BaseFieldDefinition::create('integer')
        ->setLabel(new TranslatableMarkup('ID'))
        ->setDescription(new TranslatableMarkup(
          'The ID of the OmnipediaAttachedData entity.'
        ))
        ->setReadOnly(true),

      'uuid'  => BaseFieldDefinition::create('uuid')
        ->setLabel(new TranslatableMarkup('UUID'))
        ->setDescription(new TranslatableMarkup(
          'The UUID of the OmnipediaAttachedData entity.'
        ))
        ->setReadOnly(true),

      'type'  => BaseFieldDefinition::create('list_string')
        ->setLabel(new TranslatableMarkup('Type'))
        ->setDescription(new TranslatableMarkup(
          'The type of data to attach.'
        ))
        ->setSetting('max_length', 255)
        ->setSetting(
          'allowed_values_function',
          \get_class() . '::attachedDataTypeAllowedValuesCallback'
        )
        ->setDefaultValueCallback(
          \get_class() . '::attachedDataTypeDefaultValueCallback'
        )
        ->setRequired(true)
        ->setDisplayOptions('form', [
          'type'    => 'options_buttons',
          'weight'  => -4,
        ])
        ->setDisplayOptions('view', [
          'label'   => 'above',
          'weight'  => -4,
        ]),

      'target'  => BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Target'))
        ->setDescription(new TranslatableMarkup(
          'The target to attach data to.'
        ))
        ->setSetting('max_length', 255)
        ->setRequired(true)
        ->setTranslatable(true)
        ->addConstraint('OmnipediaAttachedDataTarget')
        ->setDisplayOptions('form', [
          'type'    => 'default_widget',
          'weight'  => -6,
        ])
        ->setDisplayOptions('view', [
          'label'   => 'above',
          'weight'  => -6,
        ]),

      'content'  => BaseFieldDefinition::create('text_long')
        ->setLabel(new TranslatableMarkup('Content'))
        ->setDescription(new TranslatableMarkup(
          'The content of the attached data.'
        ))
        ->setRequired(true)
        ->setTranslatable(true)
        ->setDisplayOptions('form', [
          'type'    => 'default_widget',
          'weight'  => -5,
        ])
        ->setDisplayOptions('view', [
          'label'   => 'above',
          'weight'  => -5,
        ]),

      'date_range' => static::dateRangeBaseFieldDefinition($entityType)
        ->setDisplayOptions('form', [
          'weight'    => -3,
        ])
        ->setDisplayOptions('view', [
          'label'   => 'above',
          'weight'  => -3,
        ]),

      'uid' => BaseFieldDefinition::create('entity_reference')
        ->setLabel(new TranslatableMarkup('Author'))
        ->setDescription(new TranslatableMarkup(
          'The user who authored this attached data.'
        ))
        ->setSetting('target_type', 'user')
        ->setSetting('handler', 'default')
        ->setDisplayOptions('form', [
          'type'      => 'entity_reference_autocomplete',
          'settings'  => [
            'match_operator'  => 'CONTAINS',
            'match_limit'     => 10,
            'size'            => 60,
            'placeholder'     => '',
          ],
          'weight'    => -2,
        ]),

      'langcode'  => BaseFieldDefinition::create('language')
        ->setLabel(new TranslatableMarkup('Language code'))
        ->setDescription(new TranslatableMarkup(
          'The language code of attached data.'
        )),

      'created'   => BaseFieldDefinition::create('created')
        ->setLabel(new TranslatableMarkup('Created'))
        ->setDescription(new TranslatableMarkup(
          'The time that the attached data was created.'
        )),

      'changed'   => BaseFieldDefinition::create('changed')
        ->setLabel(new TranslatableMarkup('Changed'))
        ->setDescription(new TranslatableMarkup(
          'The time that the attached data was last edited.'
        )),
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {

    $this->set('uid', $account->id());

    return $this;

  }

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the uid entity reference to the
   * current user as the creator of the instance.
   */
  public static function preCreate(
    EntityStorageInterface $storage, array &$values
  ) {

    parent::preCreate($storage, $values);

    $values += [
      'uid' => \Drupal::currentUser()->id(),
    ];

  }

  /**
   * Gets the OmnipediaAttachedData plug-in manager service.
   *
   * @return \Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface
   *   The OmnipediaAttachedData plug-in manager service.
   */
  protected static function attachedDataManager(): OmnipediaAttachedDataManagerInterface {
    return \Drupal::service('plugin.manager.omnipedia_attached_data');
  }

  /**
   * {@inheritdoc}
   */
  public static function attachedDataTypeAllowedValuesCallback(
    FieldStorageDefinitionInterface $definition,
    OmnipediaAttachedDataInterface $entity = null,
    bool &$cacheable = true
  ): array {
    return self::attachedDataManager()->getAttachedDataTypeOptionValues();
  }

  /**
   * {@inheritdoc}
   */
  public static function attachedDataTypeDefaultValueCallback(
    OmnipediaAttachedDataInterface $entity,
    FieldDefinitionInterface $definition
  ): array {

    return [
      'value' => self::attachedDataManager()->getAttachedDataTypeDefaultValue(),
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function getTypeLabel(): TranslatableMarkup|string {

    return self::attachedDataManager()->getAttachedDataTypeLabel(
      $this->type->value
    );

  }

  /**
   * {@inheritdoc}
   */
  public function getTitle(): string {
    // We use the target field as the title.
    return $this->target->value;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->getTitle() . ' (' . $this->getTypeLabel() . ')';
  }

}
