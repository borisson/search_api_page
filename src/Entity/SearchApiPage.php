<?php

/**
 * @file
 * Contains Drupal\search_api_page\Entity\SearchApiPage.
 */

namespace Drupal\search_api_page\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\search_api_page\SearchApiPageInterface;

/**
 * Defines the Search page entity.
 *
 * @ConfigEntityType(
 *   id = "search_api_page",
 *   label = @Translation("Search page"),
 *   handlers = {
 *     "list_builder" = "Drupal\search_api_page\SearchApiPageListBuilder",
 *     "form" = {
 *       "add" = "Drupal\search_api_page\Form\SearchApiPageForm",
 *       "edit" = "Drupal\search_api_page\Form\SearchApiPageForm",
 *       "delete" = "Drupal\search_api_page\Form\SearchApiPageDeleteForm"
 *     }
 *   },
 *   config_prefix = "search_api_page",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/search_api_page/{search_api_page}",
 *     "edit-form" = "/admin/structure/search_api_page/{search_api_page}/edit",
 *     "delete-form" = "/admin/structure/search_api_page/{search_api_page}/delete",
 *     "collection" = "/admin/structure/visibility_group"
 *   }
 * )
 */
class SearchApiPage extends ConfigEntityBase implements SearchApiPageInterface {

  /**
   * The Search page ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Search page label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Search page path.
   *
   * @var string
   */
  protected $path;

  /**
   * The Search Api index.
   *
   * @var string
   */
  protected $index;

  /**
   * The limit per page.
   *
   * @var string
   */
  protected $limit = 10;

  /**
   * {@inheritdoc}
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * {@inheritdoc}
   */
  public function getIndex() {
    return $this->index;
  }

  /**
   * {@inheritdoc}
   */
  public function getLimit() {
    return $this->limit;
  }

}
