<?php

/**
 * @file
 * Contains Drupal\search_api_page\SearchApiPageListBuilder.
 */

namespace Drupal\search_api_page;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\search_api_page\Entity\SearchApiPage;

/**
 * Provides a listing of Search page entities.
 */
class SearchApiPageListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Search page');
    $header['path'] = $this->t('Path');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var $entity SearchApiPage */
    $row['label'] = $entity->label();
    $row['path'] = $entity->getPath();
    return $row + parent::buildRow($entity);
  }

}
