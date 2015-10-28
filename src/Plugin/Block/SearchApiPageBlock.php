<?php

/**
 * @file
 * Contains \Drupal\search_api_page\Plugin\Block\SearchApiPageBlock.
 */

namespace Drupal\search_api_page\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Search Api page form' block.
 *
 * @Block(
 *   id = "search_api_page_form_block",
 *   admin_label = @Translation("Search Api Page search block form"),
 *   category = @Translation("Forms")
 * )
 */
class SearchApiPageBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  /*protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'search content');
  }*/

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $options = array();

    $searchApiPages = \Drupal::entityManager()->getStorage('search_api_page')->loadMultiple();
    foreach ($searchApiPages as $searchApiPage) {
      $options[$searchApiPage->id()] = $searchApiPage->label();
    }

    $form['search_api_page'] = array(
      '#type' => 'select',
      '#title' => $this->t('Search page'),
      '#default_value' => !empty($this->configuration['search_api_page']) ? $this->configuration['search_api_page'] : '',
      '#description' => $this->t('Select to which search page a submission of this form will redirect to'),
      '#options' => $options,
      '#required' => TRUE,
    );

    return $form;
  }

  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['search_api_page'] = $form_state->getValue('search_api_page');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $args = array(
      'search_api_page' => $this->configuration['search_api_page'],
    );
    return \Drupal::formBuilder()->getForm('Drupal\search_api_page\Form\SearchApiPageBlockForm', $args);
  }

}
