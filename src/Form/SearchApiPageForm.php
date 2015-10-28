<?php

/**
 * @file
 * Contains Drupal\search_api_page\Form\SearchApiPageForm.
 */

namespace Drupal\search_api_page\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api_page\SearchApiPageInterface;

/**
 * Class SearchApiPageForm.
 *
 * @package Drupal\search_api_page\Form
 */
class SearchApiPageForm extends EntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var $search_api_page SearchApiPageInterface */
    $search_api_page = $this->entity;

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $search_api_page->label(),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $search_api_page->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\search_api_page\Entity\SearchApiPage::load',
      ),
      '#disabled' => !$search_api_page->isNew(),
    );

    $form['path'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Path'),
      '#maxlength' => 255,
      '#default_value' => $search_api_page->getPath(),
      '#description' => $this->t("Do not include the optional argument or trailing slash."),
      '#required' => TRUE,
    );

    $options = array();
    $searchApiIndexes = $this->entityManager->getStorage('search_api_index')->loadMultiple();
    /** @var  $searchApiIndex IndexInterface */
    foreach ($searchApiIndexes as $searchApiIndex) {
      $options[$searchApiIndex->id()] = $searchApiIndex->label();
    }

    $form['index'] = array(
      '#type' => 'select',
      '#title' => $this->t('Search API index'),
      '#options' => $options,
      '#default_value' => $search_api_page->getIndex(),
      '#required' => TRUE,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $search_api_page = $this->entity;
    $status = $search_api_page->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Search page.', [
          '%label' => $search_api_page->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Search page.', [
          '%label' => $search_api_page->label(),
        ]));
    }
    $form_state->setRedirectUrl($search_api_page->urlInfo('collection'));
  }

}
