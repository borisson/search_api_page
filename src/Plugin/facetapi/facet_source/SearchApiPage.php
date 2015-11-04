<?php

/**
 * @file
 *   Contains \Drupal\search_api_page\Plugin\facet_api\facet_source\SearchApiPage
 */

namespace Drupal\search_api_page\Plugin\facetapi\facet_source;

use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\facetapi\Plugin\facetapi\facet_source\SearchApiBaseFacetSource;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\search_api_page\SearchApiPageInterface;


/**
 * Represents a facet source which represents search_api_page pages.
 *
 * Most of the work of actually getting a page is done in the deriver.
 *
 * @FacetApiFacetSource(
 *   id = "search_api_page",
 *   deriver = "Drupal\search_api_page\Plugin\facetapi\facet_source\SearchApiPageDeriver"
 * )
 */
class SearchApiPage extends SearchApiBaseFacetSource {

  use DependencySerializationTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager|null
   */
  protected $entityTypeManager;

  /**
   * The typed data manager.
   *
   * @var \Drupal\Core\TypedData\TypedDataManager|null
   */
  protected $typedDataManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|null
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, $query_type_plugin_manager, $search_results_cache) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $query_type_plugin_manager, $search_results_cache);

    // Load facet plugin definition and depending on those settings; load the
    // corresponding search api page and load it's index.
    $page_id = $plugin_definition['search_api_page'];
    $page = \Drupal\search_api_page\Entity\SearchApiPage::load($page_id);
    $this->index = Index::load($page->getIndex());
  }

  /**
   * {@inheritdoc}
   */
  public function fillFacetsWithResults($facets) {
    // Check if there are results in the static cache.
    $results = $this->searchApiResultsCache->getResults($this->pluginId);

    // If there are no results, execute the search page and check for results
    // again
    if (!$results) {
      /** @var $searchApiPage SearchApiPageInterface */
      list(, $search_api_page) = explode(':', $this->pluginId);
      $searchApiPage = \Drupal\search_api_page\Entity\SearchApiPage::load($search_api_page);

      /** @var $searchApiIndex IndexInterface */
      $searchApiIndex = Index::load($searchApiPage->getIndex());

      // Create the query.
      $query = $searchApiIndex->query([
        'parse_mode' => 'direct',
        'limit' => $searchApiPage->getLimit(),
        'offset' => isset($_GET['page']) ? $_GET['page'] : 0,
        'search id' => 'search_api_page:' . $searchApiPage->id()
      ]);

      // @todo Keywords
      if (!empty($keyword)) {
        $query->keys($keyword);
      }

      // Index fields.
      $query->setFulltextFields(['rendered_item']);

      // Execute the query.
      $results = $query->execute();
    }

    // If we got results from the cache, this is the first code executed in this
    // method, so it's good to double check that we can actually work with
    // $results.
    if ($results instanceof ResultSetInterface) {
      // Get our facet data from the results.
      $facet_results = $results->getExtraData('search_api_facets');

      // Loop over each facet and execute the build method from the given query
      // type
      foreach ($facets as $facet) {
        if (isset($facet_results[$facet->getFieldIdentifier()])) {
          $configuration = array(
            'query' => NULL,
            'facet' => $facet,
            'results' => $facet_results[$facet->getFieldIdentifier()],
          );

          // Get the Facet Specific Query Type so we can process the results
          // using the build() function of the query type.
          $query_type = $this->queryTypePluginManager->createInstance($facet->getQueryType(), $configuration);
          $query_type->build();
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    /** @var $searchApiPage SearchApiPageInterface */
    list(, $search_api_page) = explode(':', $this->pluginId);
    $searchApiPage = \Drupal\search_api_page\Entity\SearchApiPage::load($search_api_page);
    return $searchApiPage->getPath();
  }

  /**
   * {@inheritdoc}
   */
  public function isRenderedInCurrentRequest() {
    $request = \Drupal::requestStack()->getMasterRequest();

    if ($request->get('_route') === str_replace(':', '.', $this->getPluginId())) {
      return TRUE;
    }
    return FALSE;
  }
}
