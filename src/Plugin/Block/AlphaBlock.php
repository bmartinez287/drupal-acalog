<?php

namespace Drupal\vscc_acalog\Plugin\Block;
use Drupal\Core\Block\BlockBase;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

/**
 * Provides an 'Alpha' block.
 *
 * @Block(
 *   id = "alpha_block",
 *   admin_label = @Translation("Alpha block"),
 *   category = @Translation("Custom Alpha block")
 * )
 */
class AlphaBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   * creates an alphabetical degrees dropdown listing
   * /academics/programs
   */
  
  public function build() {
    
    // get the current catalog ID
    $catalogID = getCatalogID();
    
    if ($catalogID > 0) {
        
      // use Guzzle to query programs from Acalog
      $client = new Client();
      
      // set variables
      $output = '';
      $programsArr = array();
          
      // build dropdown list of programs
      $request_url = 'http://volstate.apis.acalog.com/v1/search/programs?key=' . API_KEY . '&format=xml&method=listing&catalog=' . $catalogID . '&options[sort]=alpha&options[limit]=200';
      
      try {
        $response = $client->request('GET', $request_url, [
          'headers' => ['Accept' => 'application/xml'],
          'connect_timeout' => 5
        ])->getBody()->getContents();
  
        // output list of programs
        $xml = simplexml_load_string($response);
        foreach ($xml->xpath('//catalog/search/results/result') as $program) {
          $programsArr[] = array('id' => $program->id, 'name' => $program->name);
        }
      
      // error connecting to Acalog
      } catch (RequestException $e) {
        $output = '<h4>We are unable to load the program information at this time. Please visit <a href="http://catalog.volstate.edu/">our catalog</a>.</h4>';
      }
          
      // return output (uses block--AlphaBlock.html.twig template)
      return array(
        '#theme' => 'vscc_alpha',
        '#type' => 'markup',
        '#markup' => $output,
        '#catalogID' => $catalogID,
        '#programs' => $programsArr,
      );
      
    } else {
      
      return array(
        '#type' => 'markup',
        '#markup' => '<h4>The catalog is temporarily unavailable.</h4>',
      );
      
    }

  }

}