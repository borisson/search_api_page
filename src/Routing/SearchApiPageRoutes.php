<?php

/**
 * @file
 * Contains Drupal\search_api_page\Routing\SearchApiRoutes.
 */

namespace Drupal\search_api_page\Routing;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\search_api_page\Entity\SearchApiPage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

/**
 * Defines a route subscriber to register a url for serving search pages.
 */
class SearchApiPageRoutes implements ContainerInjectionInterface {

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new SearchApiRoutes object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The stream wrapper manager service.
   */
  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')
    );
  }

  /**
   * Returns an array of route objects.
   *
   * @return \Symfony\Component\Routing\Route[]
   *   An array of route objects.
   */
  public function routes() {
    $routes = array();

    /** @var $searchApiPage SearchApiPage */
    foreach ($this->entityManager->getStorage('search_api_page')->loadMultiple() as $searchApiPage) {
      $routes['search_api_page.' . $searchApiPage->id()] = new Route(
        '/' . $searchApiPage->getPath() . '/{keyword}',
        array(
          '_controller' => 'Drupal\search_api_page\Controller\SearchApiPageController::page',
          'keyword' => '',
        ),
        array(
          '_access' => 'TRUE',
        )
      );
    }

    return $routes;
  }

}

