<?php

namespace Drupal\vscc_acalog\Plugin\Block;
use Drupal\Core\Block\BlockBase;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

/**
 * Provides an 'Program' block.
 *
 * @Block(
 *   id = "program_block",
 *   admin_label = @Translation("Program block"),
 *   category = @Translation("Custom Program block")
 * )
 */
class ProgramBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    
    $output = '';
    
    // get the current catalog ID
    $catalogID = getCatalogID();
    
    if ($catalogID > 0) {
    
      // get the program name (from page title)
      $request = \Drupal::request();
      $route_match = \Drupal::routeMatch();
      if ($route_match->getParameter('node')) {
        $programName = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
        $programName = str_replace(' Courses', '', $programName);
      }
      
      // use Guzzle to query programs from Acalog
      $client = new Client();
  
      // query the program ID from Acalog
      $request_url = 'http://volstate.apis.acalog.com/v1/search/programs?key=XXXXXXXXX&format=xml&method=search&catalog=24&query="' . $programName . '"';     
      try {
        $response = $client->request('GET', $request_url, [
          'headers' => ['Accept' => 'application/xml'],
          'connect_timeout' => 5
        ])->getBody()->getContents();
        $xml = simplexml_load_string($response);
        foreach ($xml->xpath('//catalog/search/results/result') as $program) {
          $programID = $program->id;
          if ($program->name == $programName) {
            break;
          }
        }
      } catch (RequestException $e) {
        $output = '<h4>The catalog is temporarily unavailable.</h4>';
      }
      
      // return output (uses block--ProgramBlock.html.twig template)
      return [
        '#theme' => 'vscc_program',
        '#type' => 'markup',
        '#markup' => $output,
        '#catalogID' => $catalogID,
        '#programID' => $programID,
        '#attached' => array(
          'library' => array(
            'vscc_acalog/acalog-lib',
          ),
        ),
        '#cache' => [
          'max-age' => 0,   // disable caching
        ],
      ];

    } else {
      
      return array(
        '#type' => 'markup',
        '#markup' => '<h4>The catalog is temporarily unavailable.</h4>',
      );
      
    }
    
  }

}