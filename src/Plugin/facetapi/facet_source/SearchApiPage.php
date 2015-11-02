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
use Drupal\search_api\Utility;
use Drupal\search_api_page\SearchApiPageInterface;


/**
 * Represents a facet source which represents the search api views.
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


    // If our results are not there, execute the view to get the results.
    if (!$results) {
      // If there are no results, execute the view. and check for results again!
      /** @var $searchApiPage SearchApiPageInterface */
      list(, $search_api_page) = explode(':', $this->pluginId);
      $searchApiPage = \Drupal\search_api_page\Entity\SearchApiPage::load($search_api_page);

      // Page title.
      $build['#title'] = $searchApiPage->label();

      /** @var $searchApiIndex IndexInterface */
      $searchApiIndex = Index::load($searchApiPage->getIndex());

      // Create the query.
      $query = Utility::createQuery($searchApiIndex, array(
          'parse_mode' => 'direct',
          'limit' => $searchApiPage->getLimit(),
          'offset' => isset($_GET['page']) ? $_GET['page'] : 0,
        )
      );

      // Keywords.
      if (!empty($keyword)) {
        $query->keys($keyword);
      }

      // Index fields.
      $query->setFulltextFields(array('rendered_item'));

      $results = $query->execute();

      // Set the path of all facets.
      $path = $searchApiPage->getPath();
      if ($path) {
        foreach ($facets as $facet) {
          $facet->setPath($path);
        }
      }
    }

    // Get the results from the cache. It is possible it still errored out.
    // @todo figure out what to do when this errors out.
    if ($results instanceof ResultSetInterface) {
      // Get our facet data.
      $facet_results = $results->getExtraData('search_api_facets');

      // Loop over each facet and execute the build method from the given
      // query type
      foreach ($facets as $facet) {
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

  /**
   * {@inheritdoc}
   */
  public function isRenderedInCurrentRequest() {
    $request = \Drupal::requestStack()->getMasterRequest();
    if ($request->attributes->get('_controller') === 'Drupal\views\Routing\ViewPageController::handle') {
      list(, $search_api_view_id, $search_api_view_display) = explode(':', $this->getPluginId());

      if ($request->attributes->get('view_id') != $search_api_view_id || $request->attributes->get('display_id') != $search_api_view_display) {
        return FALSE;
      }
    }
    return TRUE;
  }
}
