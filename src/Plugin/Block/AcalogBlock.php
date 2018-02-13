<?php

namespace Drupal\vscc_acalog\Plugin\Block;
use Drupal\Core\Block\BlockBase;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

/**
 * Provides an 'Acalog' block.
 *
 * @Block(
 *   id = "acalog_block",
 *   admin_label = @Translation("Acalog block"),
 *   category = @Translation("Custom Acalog block")
 * )
 */
class AcalogBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    
    // get the current catalog ID
    $catalogID = getCatalogID();
    
    if ($catalogID > 0) {

      // get the division name from the URL
      $url = \Drupal::request()->getRequestUri();
      $urlArr = explode("/", $url);
      $divisionName = $urlArr[2];
      $divisionName = str_replace('-',' ',str_replace('/academics/','',$divisionName));
      $x = 1;
      $output = '';
          
      // set the Acalog query URL
      $request_url = 'http://sitename.apis.acalog.com/v1/search/programs?key=XXXXXXXXX&format=xml&method=search&catalog=' . $catalogID . '&query=parent%3A%22' . $divisionName . '%22&options[limit]=100'; 
      
      // use Guzzle to query programs from Acalog
      $client = new Client();
      
      try {
        $response = $client->request('GET', $request_url, [
          'headers' => ['Accept' => 'application/xml'],
          'connect_timeout' => 5
        ])->getBody()->getContents();
      
        $xml = simplexml_load_string($response);
        $divisionNameURL = str_replace(' ','-',$divisionName);
          
        // get number of results
        foreach ($xml->xpath('//catalog/search') as $numResults) {
          $num = $numResults->hits;
        }
        $half = round($num/2);
          
        // output list of programs
        $output = $output . '<div class="row><div class="col-sm-6">';
        $output = $output . '<ul>';
        foreach ($xml->xpath('//catalog/search/results/result') as $program) {
          $output = $output . '<li><a href="/academics/' . $program->id . '">' . trim($program->name) . '</a></li>';
          
          // display programs in two equal columns
          if ($x == $half) {
            $output = $output . '</ul></div><div class="col-sm-6"><ul>';
          }
          $x++;
        }
        $output = $output . '</ul>';
        $output = $output . '</div></div>';
      
      // error connecting to Acalog
      } catch (RequestException $e) {
        $output = '<h4>The catalog is temporarily unavailable.</h4>';
      }
      
      // return output
      return array(
        '#type' => 'markup',
        '#markup' => $output,
        '#title' => ucwords($divisionName) . ' Degrees & Certificates',
        '#cache' => [
          'max-age' => 0,   // disable caching
        ],
      );
      
    } else {
      
      // return output
      return array(
        '#type' => 'markup',
        '#markup' => '<h4>The catalog is temporarily unavailable.</h4>',
        '#title' => ' Degrees & Certificates',
      );
      
    }

  }

}