<?php

namespace Drupal\omnipedia_attached_data\PluginManager;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\omnipedia_attached_data\Annotation\OmnipediaAttachedData as OmnipediaAttachedDataAnnotation;
use Drupal\omnipedia_attached_data\PluginManager\OmnipediaAttachedDataManagerInterface;
use Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\OmnipediaAttachedDataInterface;

/**
 * The OmnipediaAttachedData plug-in manager.
 */
class OmnipediaAttachedDataManager extends DefaultPluginManager implements OmnipediaAttachedDataManagerInterface {

  /**
   * The attached data configuration settings name.
   *
   * @var string
   */
  protected const SETTINGS_CONFIG_NAME = 'omnipedia_attached_data.settings';

  /**
   * The Drupal configuration object factory service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $configFactory;

  /**
   * Creates the discovery object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plug-in
   *   implementations.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   Cache backend instance to use.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler to invoke the alter hook with.
   *
   * @see \Drupal\plugin_type_example\SandwichPluginManager
   *   This method is based heavily on the sandwich manager from the
   *   'examples' module.
   */
  public function __construct(
    \Traversable            $namespaces,
    CacheBackendInterface   $cacheBackend,
    ModuleHandlerInterface  $moduleHandler
  ) {
    parent::__construct(
      // This tells the plug-in manager to look for OmnipediaAttachedData
      // plug-ins in the 'src/Plugin/Omnipedia/AttachedData' subdirectory of any
      // enabled modules. This also serves to define the PSR-4 subnamespace in
      // which OmnipediaAttachedData plug-ins will live.
      'Plugin/Omnipedia/AttachedData',

      $namespaces,

      $moduleHandler,

      // The name of the interface that plug-ins should adhere to. Drupal will
      // enforce this as a requirement. If a plug-in does not implement this
      // interface, Drupal will throw an error.
      OmnipediaAttachedDataInterface::class,

      // The name of the annotation class that contains the plug-in definition.
      OmnipediaAttachedDataAnnotation::class
    );

    // This allows the plug-in definitions to be altered by an alter hook. The
    // parameter defines the name of the hook:
    //
    // hook_omnipedia_attached_data_info_alter()
    $this->alterInfo('omnipedia_attached_data_info');

    // This sets the caching method for our plug-in definitions. Plug-in
    // definitions are discovered by examining the directory defined above, for
    // any classes with a OmnipediaAttachedDataAnnotation::class. The
    // annotations are read, and then the resulting data is cached using the
    // provided cache backend.
    $this->setCacheBackend($cacheBackend, 'omnipedia_attached_data_info');
  }

  /**
   * {@inheritdoc}
   */
  public function setAddtionalDependencies(
    ConfigFactoryInterface $configFactory
  ): void {
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedDataSettingsConfigName(): string {
    return self::SETTINGS_CONFIG_NAME;
  }

  /**
   * Sort attached data types by their weights.
   *
   * @param array[] &$types
   *   Attached data types, keyed by their machine name and values being an
   *   array containing at least a 'weight' key.
   *
   * @see Drupal\Component\Utility\SortArray::sortByWeightElement()
   *
   * @see https://www.drupal.org/node/2181331
   */
  protected function sortAttachedDataTypes(array &$types): void {
    \uasort(
      $types,
      ['Drupal\Component\Utility\SortArray', 'sortByWeightElement']
    );
  }

  /**
   * Get attached data type weights.
   *
   * @return int[]
   *   An array of integer weights, keyed by attached data type machine names.
   */
  protected function getAttachedDataTypeWeights(): array {
    /** @var array|null */
    $weightConfig = $this->configFactory
      ->get($this->getAttachedDataSettingsConfigName())->get('type_weights');

    if (!\is_array($weightConfig)) {
      return [];
    }

    return $weightConfig;
  }

  /**
   * {@inheritdoc}
   */
  public function saveAttachedDataTypeWeights(array $types): void {
    /** @var array */
    $existingTypes = $this->getAttachedDataTypes(false);

    /** @var array */
    $typeWeights = [];

    foreach ($existingTypes as $machineName => $typeSettings) {
      if (!isset($existingTypes[$machineName])) {
        continue;
      }

      $typeWeights[$machineName] = $types[$machineName];
    }

    $this->configFactory
      ->getEditable(
        $this->getAttachedDataSettingsConfigName()
      )
      ->set('type_weights', $typeWeights)
      ->save();
  }

  /**
   * {@inheritdoc}
   *
   * @todo Can we cache most of this or is the performance impact negligible?
   */
  public function getAttachedDataTypes(bool $sorted = true): array {
    /** @var array */
    $definitions = $this->getDefinitions();

    /** @var array */
    $types = [];

    /** @var array */
    $weights = $this->getAttachedDataTypeWeights();

    foreach ($definitions as $machineName => $definition) {
      /** @var array */
      $types[$machineName] = [
        'title' => $definition['title'],
      ];

      if (isset($weights[$machineName])) {
        $types[$machineName]['weight'] = $weights[$machineName];
      } else {
        $types[$machineName]['weight'] = 0;
      }
    }

    if ($sorted === true) {
      $this->sortAttachedDataTypes($types);
    }

    return $types;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedDataTypeOptionValues(): array {
    /** @var array */
    $types = $this->getAttachedDataTypes();

    /** @var array */
    $values = [];

    foreach ($types as $machineName => $typeSettings) {
      $values[$machineName] = $typeSettings['title'];
    }

    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedDataTypeDefaultValue(): ?string {
    /** @var array */
    $types = $this->getAttachedDataTypes();

    \reset($types);

    // Returns the first types's machine name as the default value.
    return \key($types);
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedDataTypeLabel(string $machineName): string {
    /** @var array */
    $definitions = $this->getDefinitions();

    if (isset($definitions[$machineName])) {
      return $definitions[$machineName]['title'];
    } else {
      return '';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateTarget(string $pluginId, string $target): array {
    /** @var \Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\OmnipediaAttachedDataInterface */
    $instance = $this->createInstance($pluginId, []);

    return $instance->validateTarget($target);
  }

  /**
   * {@inheritdoc}
   */
  public function getContent(
    string $pluginId, string $target, $date = null
  ): ?string {
    /** @var \Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\OmnipediaAttachedDataInterface */
    $instance = $this->createInstance($pluginId, []);

    return $instance->getContent($target, $date);
  }


  /**
   * {@inheritdoc}
   */
  public function getAttachedDataTitleAttributeName(): string {
    return 'data-omnipedia-attached-data-title';
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachedDataContentAttributeName(): string {
    return 'data-omnipedia-attached-data-content';
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachments(): array {
    /** @var array */
    $attachments = [
      'drupalSettings' => ['omnipedia' => ['attachedData' => [
        'titleAttributeName'    => $this->getAttachedDataTitleAttributeName(),
        'contentAttributeName'  => $this->getAttachedDataContentAttributeName(),
      ]]],
    ];

    /** @var array */
    $definitions = $this->getDefinitions();

    foreach ($definitions as $pluginId => $definition) {
      /** @var \Drupal\omnipedia_attached_data\Plugin\Omnipedia\AttachedData\OmnipediaAttachedDataInterface */
      $instance = $this->createInstance($pluginId, []);

      /** @var array */
      $attachments = NestedArray::mergeDeep(
        $attachments,
        $instance->getAttachements()
      );
    }

    return $attachments;
  }

}
