<?php

namespace Drupal\omnipedia_attached_data\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the OmnipediaAttachedData entity edit forms.
 */
class OmnipediaAttachedDataForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /* @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface */
    $entity = $this->entity;

    $form['langcode'] = [
      '#title'          => $this->t('Language'),
      '#type'           => 'language_select',
      '#default_value'  => $entity->getUntranslated()->language()->getId(),
      '#languages'      => Language::STATE_ALL,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.omnipedia_attached_data.collection');

    /* @var \Drupal\omnipedia_attached_data\Entity\OmnipediaAttachedDataInterface */
    $entity = $this->getEntity();

    $entity->save();
  }

}
