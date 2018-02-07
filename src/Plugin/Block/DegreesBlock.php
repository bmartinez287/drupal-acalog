<?php

namespace Drupal\vscc_acalog\Plugin\Block;
use Drupal\Core\Block\BlockBase;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

/**
 * Provides an 'Degrees' block.
 *
 * @Block(
 *   id = "degrees_block",
 *   admin_label = @Translation("Degrees block"),
 *   category = @Translation("Custom Degrees block")
 * )
 */
class DegreesBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   * creates the Degrees & Certificates accordion page
   * http://beta.volstate.edu/academics/programs
   */
  
  public function build() {
    
    // get the current catalog ID
    $catalogID = getCatalogID();
    
    if ($catalogID > 0) {
    
      // set variables
      $output = '';
      $counter = 0;
      $programsArr = array();
      
      $degreeArr = array("Associate of Applied Science (A.A.S.)","Technical Certificate","Associate of Arts (A.A.) - University Parallel","Associate of Fine Arts (A.F.A.)","Associate of Science (A.S.) - University Parallel","Associate of Science in Teaching (A.S.T.)");
      
      // use Guzzle to query programs from Acalog
      $client = new Client();
      
      // loop through the degree types
      foreach ($degreeArr as $degreeType) {
        $request_url = 'http://volstate.apis.acalog.com/v1/search/programs?key=XXXXXXXXX&format=xml&method=search&catalog=' . $catalogID . '&query=degreetype%3A%22' . $degreeType . '%22&options[limit]=100';
        
        try {
          $response = $client->request('GET', $request_url, [
            'headers' => ['Accept' => 'application/xml'],
            'connect_timeout' => 5
          ])->getBody()->getContents();
    
          // output list of programs
          $xml = simplexml_load_string($response);
          foreach ($xml->xpath('//catalog/search/results/result') as $program) {
            $programsArr[$counter][] = array('id' => $program->id, 'name' => $program->name);
          }
        
        // error connecting to Acalog
        } catch (RequestException $e) {
          $output = '<h4>We are unable to load the program information at this time. Please visit <a href="http://catalog.volstate.edu/">our catalog</a>.</h4>';
        }
        
        $counter = $counter + 1;
      }  // foreach
      
      // return output (uses block--DegreesBlock.html.twig template)
      return array(
        '#theme' => 'vscc_acalog',
        '#type' => 'markup',
        '#markup' => $output,
        '#degrees' => $degreeArr,
        '#programs' => $programsArr,
        '#title' => 'Degrees & Certificates',
      );
    
    } else {
      
      return array(
        '#type' => 'markup',
        '#markup' => '<h4>The catalog is temporarily unavailable.</h4>',
      );
      
    }
  }

}