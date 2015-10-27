<?php

/**
 * @Contains \Drupal\search_api_page\Controller\SearchApiPageController.
 */

namespace Drupal\search_api_page\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Defines a controller to serve search pages.
 */
class SearchApiPageController extends ControllerBase {


  public function page($search_api_page, $keyword = '') {

    return array(
      '#title' => 'Search',
      '#markup' => 'search page: ' . $search_api_page . ' - keyword: ' . $keyword,
    );

  }

}
