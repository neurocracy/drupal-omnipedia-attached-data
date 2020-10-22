<?php

namespace Drupal\omnipedia_attached_data\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface;

/**
 * Defines the OmnipediaAttachedData entity.
 *
 * @ContentEntityType(
 *   id           = "omnipedia_attached_data",
 *   label        = @Translation("Omnipedia: Attached data"),
 *   base_table   = "omnipedia_attached_data",
 *   entity_keys  = {
 *     "id"   = "id",
 *     "uuid" = "uuid",
 *   },
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\omnipedia_attached_data\Form\OmnipediaAttachedDataForm",
 *     },
 *     "access" = "Drupal\omnipedia_attached_data\Access\OmnipediaAttachedDataAccessControlHandler",
 *   },
 * )
 */
class OmnipediaAttachedData extends ContentEntityBase implements OmnipediaAttachedDataInterface {

  use EntityChangedTrait;

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

      'type'  => BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Type'))
        ->setDescription(new TranslatableMarkup(
          'The type of the OmnipediaAttachedData entity.'
        ))
        ->setSetting('max_length', 255)
        ->setRequired(true),

      'target'  => BaseFieldDefinition::create('string')
        ->setLabel(new TranslatableMarkup('Target'))
        ->setDescription(new TranslatableMarkup(
          'The target of the OmnipediaAttachedData entity.'
        ))
        ->setSetting('max_length', 255)
        ->setRequired(true)
        ->setTranslatable(true),

      'content'  => BaseFieldDefinition::create('text_long')
        ->setLabel(new TranslatableMarkup('Content'))
        ->setDescription(new TranslatableMarkup(
          'The content of the OmnipediaAttachedData entity.'
        ))
        ->setRequired(true)
        ->setTranslatable(true),

      'date_start'  => BaseFieldDefinition::create('datetime')
        ->setLabel(new TranslatableMarkup('Start date'))
        ->setDescription(new TranslatableMarkup(
          'The earliest date this OmnipediaAttachedData entity can be attached to. If this is empty, will always be attached to the earliest available date.'
        )),

      'date_end'  => BaseFieldDefinition::create('datetime')
        ->setLabel(new TranslatableMarkup('End date'))
        ->setDescription(new TranslatableMarkup(
          'The last date this OmnipediaAttachedData entity can be attached to. If this is empty, will always be attached to the last available date.'
        )),

      'uid' => BaseFieldDefinition::create('entity_reference')
        ->setLabel(new TranslatableMarkup('Author'))
        ->setDescription(new TranslatableMarkup(
          'The username of the content author.'
        ))
        ->setSetting('target_type', 'user')
        ->setSetting('handler', 'default'),

      'langcode'  => BaseFieldDefinition::create('language')
        ->setLabel(new TranslatableMarkup('Language code'))
        ->setDescription(new TranslatableMarkup(
          'The language code of OmnipediaAttachedData entity.'
        )),

      'created'   => BaseFieldDefinition::create('created')
        ->setLabel(new TranslatableMarkup('Created'))
        ->setDescription(new TranslatableMarkup(
          'The time that the OmnipediaAttachedData entity was created.'
        )),

      'changed'   => BaseFieldDefinition::create('changed')
        ->setLabel(new TranslatableMarkup('Changed'))
        ->setDescription(new TranslatableMarkup(
          'The time that the OmnipediaAttachedData entity was last edited.'
        )),
    ];
  }

}
