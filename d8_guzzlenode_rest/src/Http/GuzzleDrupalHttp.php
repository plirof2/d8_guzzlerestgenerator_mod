<?php

namespace Drupal\d8_guzzlenode_rest\Http;

use GuzzleHttp\Client;
use GuzzleHttp;
use GuzzleHttp\Psr7\Request;
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
         // if($matches[0]!=null) \Drupal::logger('HEADERS got ')->notice("$matches[0]=$matches[1] ,AAAAAAAAAAAA row=$row  , requestHeaders=$requestHeaders"); 
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
      
      //\Drupal::logger('GUZRESTGEN-ORIG before switch')->notice("res".json_decode($res,true) ." |||||||||  requestMethod=$requestMethod"); 
      //if ($requestMethod=='POST') \Drupal::logger('GUZRESTGEN-ORIG POSTTTTT')->notice("res".json_decode($res,true) ." |||||||||  requestMethod=$requestMethod"); 
      switch($requestMethod){
        case "GET":
          //\Drupal::logger('CASE GET')->notice("res".json_decode($res,true) ."||| requestPayloadData=$requestPayloadData |||||||||  requestMethod=$requestMethod"); 
          $res = $client->get($requestUrl, [
            'http_errors' => false,
            'headers' => $headers
          ]);
        
        break;
        case "POST":
            \Drupal::logger('d8_guzzlenode_rest')->notice("OWN GuzzleDrupalHttp requestPayloadData=$requestPayloadData  ||| res".json_decode($res,true) ." |||||||||  requestMethod=$requestMethod"); 
            //$res = $client->request('POST', $requestUrl, ['json' => ['foo' => 'bar']]); //ok works
            // NOTE *** $body must be a VALID be a JSON string
            // RELAY JSON from $body { "userId": "9", "title": "AAAAAAAAAAtitle", "body": "AAAAAAAAAAAAABODY aliquidmagnam sint", "id": 101 } //THIS IS VALID JSON
            //$res = $client->request('POST', $requestUrl, [ 'json' => ["userId"=> '9', "title"=> "AAAAAAAAAAtitle", "body"=> "AAAAAAAAAAAAABODY aliquidmagnam sint"] ]);  //ΟΚ Works 100%
        		//NOTE *** REMOVE $body PARSING FROM above          **
        		// SAMPLE CODE that accepts : "userId": '9',"title": "AAAAAAAAAAtitle", "body": "AAAAAAAAAAAAABODY aliquidmagnam sint"
        		$string = $body; //"business_type,cafe|business_type_plural,cafes|sample_tag,couch|business_name,couch cafe";
        		///$string = str_replace(array("\n", "\r" , '"',"'"), '', $body); //remove \n \r and "
           // $string = str_replace(array("\n", "\r" ), '', $body); //remove \n \r and "  ?????????? Maybe remove this
            /*$string='
            {
  "type":[{"target_id":"article",
            "target_type": "node_type",
            "target_uuid": "ff9b4624-1224-49f6-be47-d19a22664f8e"
}],
  "title":[{"value":"Hello World222222222"}],
  "body":[{"value":"How are you?222222222"}]
} 
            ' ;  */
        		$finalArray=json_decode($string,true);
            
           // \Drupal::logger('GUZRESTGEN-ORIG POSTTTTT22222 bbb ')->notice("res=".json_encode($body,true) ." ||||||||| requestUrl=$requestUrl  |||| requestMethod=$requestMethod"); 
            //$response = $check->performRequest($requestUrl, 'POST', $requestHeaders, $requestPayloadData);
            //$response = $client->request('POST', $requestUrl, ['json' => $finalArray ,'headers' => $headers ]);

           // \Drupal::logger('CASE POST 3333')->notice(" ||headers=".json_encode($headers,true)." ||||||||| requestUrl=$requestUrl  ||| requestMethod=$requestMethod ||| finalarray=".json_encode($finalArray,true));   
            $res = $client->request('POST', $requestUrl,['json' => $finalArray ,'headers' => $headers ]); //OK Works
            

            \Drupal::logger('CASE POST 444')->notice(" ||headers=".json_encode($headers,true)." ||||||||| requestUrl=$requestUrl  ||| requestMethod=$requestMethod ||| finalarray=".json_encode($finalArray,true));   
           
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
          \Drupal::logger('GUZRESTGEN-ORIG DEFAULT switch')->notice("res".json_decode($res,true) ." |||||||||  requestMethod=$requestMethod"); 
      }


      \Drupal::logger('GUZRESTGEN-ORIG AFTER switch')->notice("res".json_decode($res,true) ." |||||||||  requestMethod=$requestMethod"); 
      return($res);
    } catch (RequestException $e) {
      return($this->t('Error'));
    }
  }
}