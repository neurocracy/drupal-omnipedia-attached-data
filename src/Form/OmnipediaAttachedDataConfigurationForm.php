<?php

namespace Drupal\omnipedia_attached_data\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface;
use Drupal\omnipedia_attached_data\Plugin\OmnipediaAttachedDataPluginCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the Omnipedia attached data configuration form.
 */
class OmnipediaAttachedDataConfigurationForm extends ConfigFormBase {

  /**
   * The OmnipediaAttachedData plug-in manager.
   *
   * @var \Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface
   */
  protected $attachedDataManager;

  /**
   * Constructs an OmnipediaAttachedDataConfigurationForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The factory for configuration objects.
   *
   * @param \Drupal\omnipedia_attached_data\OmnipediaAttachedDataManagerInterface $attachedDataManager
   *   The OmnipediaAttachedData plug-in manager.
   */
  public function __construct(
    ConfigFactoryInterface                $configFactory,
    OmnipediaAttachedDataManagerInterface $attachedDataManager
  ) {
    parent::__construct($configFactory);

    // Save dependencies.
    $this->attachedDataManager = $attachedDataManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.omnipedia_attached_data')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'omnipedia_attached_data_configuration';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      $this->attachedDataManager->getAttachedDataSettingsConfigName(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var array */
    $types = $this->attachedDataManager->getAttachedDataTypes();

    /** @var array */
    $form['type_weights'] = [
      '#type'     => 'table',
      '#header'   => [
        $this->t('Type'),
        $this->t('Weight'),
      ],
      '#empty'    => $this->t('No attached data types are available.'),
      '#attributes' => [
        'class' => ['attached-data__order-table'],
      ],
      '#tabledrag' => [
        [
          'action'        => 'order',
          'relationship'  => 'sibling',
          // This is the class of the weight form element for each row. Table
          // drag needs this to know what field to save the updated weight to.
          'group'     => 'attached-data__weight',
        ],
      ],
    ];

    foreach ($types as $machineName => $typeSettings) {
      /** @var array */
      $form['type_weights'][$machineName] = [
        '#attributes' => [
          'class' => ['draggable'],
        ],

        '#weight'     => $typeSettings['weight'],

        'type'  => [
          '#markup'  => $typeSettings['title'],
        ],

        'weight'  => [
          '#type'       => 'weight',
          '#title'      => $this->t(
            'Weight for @type', ['@type' => $typeSettings['title']]
          ),
          '#title_display'  => 'invisible',
          '#default_value'  => $typeSettings['weight'],
          '#attributes'   => [
            // This must be the same as the 'group' setting in the table's
            // #tabledrag array.
            'class' => ['attached-data__weight'],
          ],
        ],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var array */
    $weights = [];

    foreach ($form_state->getValue('type_weights') as $machineName => $value) {
      $weights[$machineName] = $value['weight'];
    }

    $this->attachedDataManager->saveAttachedDataTypeWeights($weights);

    parent::submitForm($form, $form_state);
  }

}
