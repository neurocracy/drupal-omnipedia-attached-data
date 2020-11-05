<?php

namespace Drupal\omnipedia_attached_data\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Form controller for the OmnipediaAttachedData entity delete form.
 */
class OmnipediaAttachedDataDeleteForm extends ContentEntityConfirmFormBase {

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

    $this->logger('omnipedia_attached_data')->notice(
      'Deleted attached data "%title".',
      [
        '%title' => $this->entity->getTitle(),
      ]);

    $form_state->setRedirect('entity.omnipedia_attached_data.collection');
  }

}
