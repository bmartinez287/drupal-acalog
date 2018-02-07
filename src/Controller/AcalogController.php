<?php

namespace Drupal\vscc_acalog\Controller;

use Drupal\Core\Controller\ControllerBase;

class AcalogController extends ControllerBase {
  
  /**
   * Display a specific program using the Acalog widget.
   * Input: programID
   * URL: /academics/{programID}
   *
   * @return array
   */
   public function program(String $programID) {
     
      // get the current catalog ID
      $catalogID = getCatalogID();
    
      $build['program'] = [
      '#markup' => '
        <div class="catalog-print"><a href="//catalog.volstate.edu/preview_program.php?catoid=' . $catalogID . '&poid=' . $programID . '&print" target="_blank"><i class="fa fa-print fa-2x" aria-hidden="true" title="print"></i><span class="sr-only">print</span></a></div>
        <div class="acalog" data-acalog-data="programs" data-acalog-catalog-legacy-id="' . $catalogID . '" data-acalog-program-legacy-id="' . $programID . '">Loading...</div>',
    ];
    
      $build['program']['#attached']['library'][] = 'vscc_acalog/acalog-lib';
    
      return $build;
    
  }
  
  /**
   * Display a specific page using the Acalog widget.
   * Input: pageName
   * URL: /catalog/{pageName}
   *
   * @return array
   */
   public function page(String $pageName) {
     
    // get the current catalog ID
    $catalogID = getCatalogID();
    
    // replace dashes with spaces
    $pageName = str_replace('-', ' ', $pageName);
      
    $build['page'] = [
    '#markup' => '<div class="acalog" data-acalog-data="pages" data-acalog-catalog-legacy-id="' . $catalogID . '" data-acalog-page-name="' . $pageName . '">Loading...</div>',
    ];
    
    $build['page']['#attached']['library'][] = 'vscc_acalog/acalog-lib';
    
    return $build;
    
  }
  
  /**
   * Display a specific degree using the Acalog widget.
   * Input: degreeName
   * URL: /catalog/degree/{degreeName}
   *
   * @return array
   */
   public function degree(String $degreeName) {
     
    // get the current catalog ID
    $catalogID = getCatalogID();
    
    $build['page'] = [
    '#markup' => '<div class="acalog" data-acalog-data="programs" data-acalog-catalog-legacy-id="' . $catalogID . '" data-acalog-program-name="' . $degreeName . '">Loading...</div>',
    ];
    
    $build['page']['#attached']['library'][] = 'vscc_acalog/acalog-lib';
    
    return $build;
    
  }

}