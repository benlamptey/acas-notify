<?php

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'LandingPages' block.
 * @Block(
 *   id = "landing_pages_block",
 *   admin_label = @Translation("Landing Pages block"),
 * )
 */
class LandingPages extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $children = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadChildren($node->get('field_taxonomy')->target_id);
    $termIds = [];
    foreach ($children as $key => $value) {
      $termIds[$key] = $key;
    }
    $query = \Drupal::database()->select('taxonomy_index', 'ti');
    $query->fields('ti', array('nid'));
    $query->condition('ti.tid', $termIds, 'IN');
    $query->distinct(TRUE);
    $result = $query->execute();
    $output = '';
    if($nodeIds = $result->fetchCol()){
      $nodes = \Drupal\node\Entity\Node::loadMultiple($nodeIds);
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
      foreach($nodes as $node) {
        $view = $view_builder->view($node, 'teaser');
        $output .= drupal_render($view);
      }
    }
    return ['#markup' => $output];
  }
  
  

}
