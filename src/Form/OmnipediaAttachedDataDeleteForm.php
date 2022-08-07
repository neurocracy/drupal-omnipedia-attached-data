<?php

declare(strict_types=1);

namespace Drupal\omnipedia_attached_data\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the OmnipediaAttachedData entity delete form.
 *
 * Note that even though ContentEntityForm uses MessengerTrait, which is
 * supposed to provide the 'messenger' service, the dependency is not saved
 * unless one calls MessengerTrait::messenger() to get it, and then it uses the
 * \Drupal static class. Because of this, we instead inject the service into the
 * form constructor as per Drupal best practices.
 *
 * @see \Drupal\Core\Messenger\MessengerTrait::messenger()
 *   Returns the messenger service using the \Drupal static class rather than
 *   via dependency injection.
 */
class OmnipediaAttachedDataDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * Our logger channel.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $loggerChannel;

  /**
   * Form constructor; saves dependencies.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entityRepository
   *   The Drupal entity repository service.
   *
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entityTypeBundleNnfo
   *   The Drupal entity type bundle service.
   *
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The Drupal time service.
   *
   * @param \Psr\Log\LoggerInterface $loggerChannel
   *   Our logger channel.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The Drupal messenger service.
   */
  public function __construct(
    EntityRepositoryInterface     $entityRepository,
    EntityTypeBundleInfoInterface $entityTypeBundleNnfo = null,
    TimeInterface                 $time = null,
    LoggerInterface               $loggerChannel,
    MessengerInterface            $messenger
  ) {

    parent::__construct($entityRepository, $entityTypeBundleNnfo, $time);

    $this->loggerChannel  = $loggerChannel;
    $this->messenger      = $messenger;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('logger.channel.omnipedia_attached_data'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t(
      'Are you sure you want to delete attached data "%title"?',
      ['%title' => $this->entity->getTitle()]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.omnipedia_attached_data.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    /* @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface */
    $entity = $this->getEntity();

    $entity->delete();

    /** @var \Drupal\Core\StringTranslation\TranslatableMarkup */
    $message = $this->t(
      'Deleted attached data "%title".',
      [
        '%title' => $this->entity->getTitle(),
      ]
    );

    $this->messenger()->addStatus($message);

    $this->loggerChannel->notice($message);

    $form_state->setRedirect('entity.omnipedia_attached_data.collection');

  }

}
