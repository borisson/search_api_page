<?php

/**
 * @file
 * Contains Drupal\search_api_page\SearchApiPageInterface.
 */

namespace Drupal\search_api_page;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Search page entities.
 */
interface SearchApiPageInterface extends ConfigEntityInterface {

  /**
   * Return the path.
   *
   * @return string
   */
  public function getPath();

  /**
   * Return the path.
   *
   * @return string
   */
  public function getIndex();

}
