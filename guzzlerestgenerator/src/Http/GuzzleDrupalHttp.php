<?php

namespace Drupal\guzzlerestgenerator\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/** 
 * Get a response code from any URL using Guzzle in Drupal 8!
 * 
 * Usage: 
 * In the head of your document:
 * 
 * use Drupal\guzzle_rest\Http\GuzzleDrupalHttp;
 * 
 * In the area you want to return the result, using any URL for $url:
 *
 * $check = new GuzzleDrupalHttp();
 * $response = $check->performRequest($requestUrl, $requestMethod, $requestHeaders, $requestPayloadData);
 *  
 **/

class GuzzleDrupalHttp {
  use StringTranslationTrait;
  
  public function performRequest($requestUrl, $requestMethod = 'GET', $requestHeaders = '', $requestPayloadData = '') {
    $client = new \GuzzleHttp\Client();
    try {
      
      // Massage $requestHeaders to generate $headers array, ready for REST call
      $requestHeaders = preg_replace(array('/\n/', '/\r/'), '#PH#', $requestHeaders);
      //foreach(explode("\r\n", $requestHeaders) as $row) {
      foreach(explode('#PH#', $requestHeaders) as $row) { 
         $matches= explode(':', $row);
         if($matches[0]!=null) $headers[$matches[0]] = $matches[1];  //jon 180222a
          /*
          if(preg_match('/(.*?): (.*)/', $row, $matches)) {
              $headers[$matches[0]] = $matches[1];

          } */
          if($matches[0]!=null) \Drupal::logger('HEADERS got ')->notice("$matches[0]=$matches[1] ,AAAAAAAAAAAA row=$row  , requestHeaders=$requestHeaders"); 
      }
      

      if($requestPayloadData != ''){
      	$body=$requestPayloadData;
        // Massage $requestPayloadData to generate $body array, ready for REST call
        /*  ORIGINAL code
        foreach(explode("\r\n", $requestPayloadData) as $row) {
            if(preg_match('/(.*?): (.*)/', $row, $matches)) {
                $body[$matches[1]] = $matches[2];
            }
        }
        */ //END of  ORIGINAL code
      }else{
        $body = '';
      }
      
      switch($requestMethod){
        case 'GET':
          $res = $client->get($requestUrl, [
            'http_errors' => false,
            'headers' => $headers
          ]);
      
          break;
        case 'POST':
        //$res = $client->request('POST', $requestUrl, ['json' => ['foo' => 'bar']]); //ok works
        // NOTE *** $body must be a VALID be a JSON string
        // RELAY JSON from $body { "userId": "9", "title": "AAAAAAAAAAtitle", "body": "AAAAAAAAAAAAABODY aliquidmagnam sint", "id": 101 } //THIS IS VALID JSON

        //$res = $client->request('POST', $requestUrl, [ 'json' => ["userId"=> '9', "title"=> "AAAAAAAAAAtitle", "body"=> "AAAAAAAAAAAAABODY aliquidmagnam sint"] ]);  //ΟΚ Works 100%

    		//NOTE *** REMOVE $body PARSING FROM above          **
    		// SAMPLE CODE that accepts : "userId": '9',"title": "AAAAAAAAAAtitle", "body": "AAAAAAAAAAAAABODY aliquidmagnam sint"
    		$string = $body; //"business_type,cafe|business_type_plural,cafes|sample_tag,couch|business_name,couch cafe";
    		///$string = str_replace(array("\n", "\r" , '"',"'"), '', $body); //remove \n \r and "
       // $string = str_replace(array("\n", "\r" ), '', $body); //remove \n \r and "  ?????????? Maybe remove this
    		$finalArray=json_decode($string);

        $res = $client->request('POST', $requestUrl,(array("json" => $finalArray))); //OK Works
        

        /*  //ORIGINAL LINE 'body' json deprecated
          $res = $client->post($requestUrl, [
            'http_errors' => false,
            'headers' => $headers,
            //'body' => $body

          ] //,[    GuzzleHttp\RequestOptions::JSON => ['foo' => 'bar']]
        );
          */
          break;
        case 'PUT':
          $res = $client->put($requestUrl, [
              'http_errors' => false,
              'headers' => $headers,
              'body' => $body
          ]);
          break;
        default:
          throw new Exception('Invalid Request Method');
      }
      return($res);
    } catch (RequestException $e) {
      return($this->t('Error'));
    }
  }
}