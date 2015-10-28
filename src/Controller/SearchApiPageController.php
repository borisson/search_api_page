<?php

/**
 * @Contains \Drupal\search_api_page\Controller\SearchApiPageController.
 */

namespace Drupal\search_api_page\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Plugin\DataType\EntityAdapter;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api_page\Entity\SearchApiPage;
use Drupal\search_api_page\SearchApiPageInterface;
use Drupal\search_api\Utility;

/**
 * Defines a controller to serve search pages.
 */
class SearchApiPageController extends ControllerBase {

  public function page($search_api_page, $keyword = '') {
    $build = array();

    /** @var $searchApiPage SearchApiPageInterface */
    $searchApiPage = SearchApiPage::load($search_api_page);

    // Page title.
    $build['#title'] = $searchApiPage->label();

    /** @var $searchApiIndex IndexInterface */
    $searchApiIndex = Index::load($searchApiPage->getIndex());

    // Create the query.
    $query = Utility::createQuery($searchApiIndex, array(
        'parse_mode' => 'direct',
        'limit' => $searchApiPage->getLimit()
      )
    );

    // Keywords.
    if (!empty($keyword)) {
      $query->keys($keyword);
    }

    // Index fields.
    $query->setFulltextFields(array('rendered_item'));

    $result = $query->execute();
    $items = $result->getResultItems();

    /** @var $item ItemInterface*/
    $results = array();
    foreach ($items as $item) {

      list(, $path, $langcode) = explode(':', $item->getId());
      list($entity_type, $id) = explode('/', $path);

      $entity = $this->entityManager()->getStorage($entity_type)->load($id);
      $results[] = $this->entityManager()->getViewBuilder($entity_type)->view($entity, 'teaser');
    }

    if (!empty($results)) {
      $build['results'] = $results;
    }

    return $build;
  }

}
