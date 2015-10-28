<?php

/**
 * @file
 * Contains \Drupal\search_api_page\Form\SearchApiPageBlockForm.
 */

namespace Drupal\search_api_page\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Drupal\search\SearchPageRepositoryInterface;
use Drupal\search_api_page\Entity\SearchApiPage;
use Drupal\search_api_page\SearchApiPageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the search form for the search api page block.
 */
class SearchApiPageBlockForm extends FormBase {

  /**
   * The search page repository.
   *
   * @var \Drupal\search\SearchPageRepositoryInterface
   */
  protected $searchPageRepository;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a new SearchBlockForm.
   *
   * @param \Drupal\search\SearchPageRepositoryInterface $search_page_repository
   *   The search page repository.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Render\RendererInterface
   *   The renderer.
   */
//  public function __construct(SearchPageRepositoryInterface $search_page_repository, ConfigFactoryInterface $config_factory, RendererInterface $renderer) {
  public function __construct(RendererInterface $renderer) {
    //$this->searchPageRepository = $search_page_repository;
    //$this->configFactory = $config_factory;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      //$container->get('search.search_page_repository'),
      //$container->get('config.factory'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'search_api_page_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $args = array()) {
    // Set up the form to submit using GET to the correct search page.
    /*$entity_id = $this->searchPageRepository->getDefaultSearchPage();
    if (!$entity_id) {
      $form['message'] = array(
        '#markup' => $this->t('Search is currently disabled'),
      );
      return $form;
    }*/

    /** @var $searchApiPage SearchApiPageInterface */
    $searchApiPage = SearchApiPage::load($args['search_api_page']);
    $route = 'search_api_page.' . $searchApiPage->id();
    $form['#action'] = $this->url($route);

    $form['search_api_page'] = array(
      '#type' => 'value',
      '#value' => $searchApiPage->id(),
    );

    $form['keys'] = array(
      '#type' => 'search',
      '#title' => $this->t('Search'),
      '#title_display' => 'invisible',
      '#size' => 15,
      '#default_value' => '',
      '#attributes' => array('title' => $this->t('Enter the terms you wish to search for.')),
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    );

    // SearchPageRepository::getDefaultSearchPage() depends on search.settings.
    //$this->renderer->addCacheableDependency($form, $this->configFactory->get('search.settings'));

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // This form submits to the search page, so processing happens there.
    $keyword = $form_state->getValue('keys');
    /** @var $searchApiPage SearchApiPageInterface */
    $form_state->setRedirectUrl(Url::fromRoute('search_api_page.' . $form_state->getValue('search_api_page'), array('keyword' => $keyword)));
  }
}
