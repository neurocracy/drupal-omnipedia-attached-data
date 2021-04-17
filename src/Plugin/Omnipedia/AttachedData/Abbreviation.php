<?php

namespace Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData;

use Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\OmnipediaAttachedDataBase;

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
}
