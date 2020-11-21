<?php

namespace Drupal\omnipedia_attached_data_migrate\Plugin\migrate\process;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\omnipedia_core\Service\Timeline;

/**
 * Provides a process plug-in to convert Omnipedia attached data dates.
 *
 * In Drupal 7, the start and end dates were stored in the datetime format, i.e.
 * they also contained the time (which was just 00:00:00). However, we now store
 * only the date in the database, and the Timeline service expects only the date
 * when building DrupalDateTime objects, so transforming the value using this
 * plug-in is preferable rather than adding additional code to the Timeline
 * service to detect the format and have to do additional work possibly many
 * times in a single request.
 *
 * @MigrateProcessPlugin(
 *   id = "omnipedia_attached_data_date"
 * )
 */
class OmnipediaAttachedDataDate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform(
    $value,
    MigrateExecutableInterface $migrateExecutable,
    Row $row,
    $destinationProperty
  ) {
    if ($value === null) {
      return $value;
    }

    try {
      /** @var \Drupal\Core\Datetime\DrupalDateTime */
      $dateObject = DrupalDateTime::createFromFormat(
        // Note that we have to use a hard-coded format rather than
        // DateTimeItemInterface::DATETIME_STORAGE_FORMAT (which is close but
        // would still throw an error) or DateTimePlus::FORMAT (which is unclear
        // on whether it's intended to be used as a database format or if it may
        // change at a future date).
        'Y-m-d H:i:s',
        $value
      );

    } catch (\Exception $exception) {
      throw new MigrateException(
        'DrupalDateTime::createFromFormat() exception:' .
        $exception->getMessage() .
        "\n" . 'Date value was: ' . $value);
    }

    if ($dateObject->hasErrors()) {
      throw new MigrateException(
        'There were one or more errors in constructing a \Drupal\Core\Datetime\DrupalDateTime object:' .
        "\n" . implode("\n", $dateObject->getErrors())
      );

    } else {
      return $dateObject->format(Timeline::DATE_FORMAT_STORAGE);
    }
  }

}
