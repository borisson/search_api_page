<?php

/**
 * @file
 * Contains Drupal\search_api_page\SearchApiPageListBuilder.
 */

namespace Drupal\search_api_page;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\search_api_page\Entity\SearchApiPage;

/**
 * Provides a listing of Search page entities.
 */
class SearchApiPageListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Title');
    $header['path'] = $this->t('Path');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var $entity SearchApiPage */
    $row['label'] = $entity->label();
    $row['path'] = \Drupal::l($entity->getPath(), Url::fromRoute('search_api_page.' . $entity->id()));
    return $row + parent::buildRow($entity);
  }

}
