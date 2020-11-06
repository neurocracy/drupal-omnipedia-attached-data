<?php

namespace Drupal\omnipedia_attached_data\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the OmnipediaAttachedData entity add and edit forms.
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
class OmnipediaAttachedDataForm extends ContentEntityForm {

  /**
   * Constructs a OmnipediaAttachedDataForm object.
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
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The Drupal messenger service.
   */
  public function __construct(
    EntityRepositoryInterface     $entityRepository,
    EntityTypeBundleInfoInterface $entityTypeBundleNnfo = null,
    TimeInterface                 $time = null,
    MessengerInterface            $messenger
  ) {
    parent::__construct($entityRepository, $entityTypeBundleNnfo, $time);

    // Save dependencies.
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   *
   * This organizes the secondary options for attached data into groups much
   * like the node edit form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /* @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface */
    $entity = $this->entity;

    $form['advanced'] = [
      '#type'         => 'vertical_tabs',
      '#default_tab'  => 'edit-attached-data-information',
    ];

    $form['attached_data_information'] = [
      '#type'   => 'details',
      '#title'  => $this->t('Attached data information'),
      '#group'  => 'advanced',
      '#weight' => -10,

      'type'        => $form['type'],
      'date_range'  => $form['date_range'],

      'langcode'    => [
        '#title'          => $this->t('Language'),
        '#type'           => 'language_select',
        '#default_value'  => $entity->getUntranslated()->language()->getId(),
        '#languages'      => Language::STATE_ALL,
      ],
    ];

    $form['authoring_information'] = [
      '#type'   => 'details',
      '#title'  => $this->t('Authoring information'),
      '#group'  => 'advanced',

      'uid'     => $form['uid'],
    ];

    // Delete the top-level keys now that we've copied them into their groups.
    unset($form['uid'], $form['type'], $form['date_range']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.omnipedia_attached_data.collection');

    /* @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface */
    $entity = $this->getEntity();

    // Save the new or updated status here because it will always be false after
    // saving. This is preferable because we may need to do stuff with the
    // entity that requires it existing (i.e. generating a link), but that won't
    // be possible to do before it's saved.
    /** @var bool */
    $isNew = $entity->isNew();

    $entity->save();

    // Create a message depending on whether attached data was added or updated.
    //
    // @todo Include a link to the attached data? TranslatableMarkup doesn't
    //   like arrays used as placeholder values, so we probably have to render
    //   $entity->toLink() ourselves, to ensure the link can be altered by hooks
    //   rather than embedding it directly in the untranslated string.
    if ($isNew) {
      /** @var \Drupal\Core\StringTranslation\TranslatableMarkup */
      $message = $this->t(
        'Added attached data "%title".',
        [
          '%title' => $entity->getTitle(),
        ]
      );
    } else {
      /** @var \Drupal\Core\StringTranslation\TranslatableMarkup */
      $message = $this->t(
        'Updated attached data "%title".',
        [
          '%title' => $entity->getTitle(),
        ]
      );
    }

    $this->messenger()->addStatus($message);

    $this->logger('omnipedia_attached_data')->notice($message);
  }

}
