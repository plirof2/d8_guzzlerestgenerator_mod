<?php

namespace Drupal\d8_guzzlenode_rest\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;

use Drupal\guzzlerestgenerator\Http\GuzzleDrupalHttp; // DEPENDS on guzzlerestgenerator module
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "guzzlenode_rest_resource",
 *   label = @Translation("Guzzlenode rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/relay"
 *   }
 * )
 */
class GuzzlenodeRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * Constructs a new GuzzlenodeRestResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user,
    Request $currentRequest //Added by Jon 180213
    ) {

    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
    $this->currentRequest = $currentRequest;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('d8_guzzlenode_rest'),
      $container->get('current_user'),
      $container->get('request_stack')->getCurrentRequest() //Added by Jon 180213
    );
  }

  /**
   * Responds to GET requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get() {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }



      //$entities = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple(); //ORIG WORKED

      $entities = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties(['type' => 'guzzle_rest']); //LOAD ONLY guzzle nodes
        //->loadMultiple();
         //$entities = \Drupal::entityTypeManager()->getStorage('external_entity_type')->loadByProperties(['type' => 'extent001']); // get specific entity JON TEST

      $get_node_id = $this->currentRequest->get('id1'); //get URL REST node id drupal.com/restapi/?myname=john  
      // JON NOTE : BUG at this moment it shows ONLY 1 guzzlenode (even if you have more)  
      foreach ($entities as $entity) {
        // $result[$entity->id()] = $entity->title->value; // OK WORKS returns all titles
        
        

        if(($entity->getType() == 'guzzle_rest') && ( $entity->id()==$get_node_id) ){

         //$result[$entity->id()] = $entity->title->value;
              // Retrieve node fields to perform REST request
              $endpoint_url = $entity->get('field_guzzle_endpoint_url')->uri;
              $request_method = $entity->get('field_guzzle_request_method')->value;
              $raw_headers = $entity->get('field_guzzle_raw_headers')->value;
              $payload_data = $entity->get('field_guzzle_data_payload')->value;
              //$status = $entity->get('status')->value; //JON try to get Published status @@@@@@@@@@@@@@

              // Perform GuzzleDrupalHttp request
              $check = new GuzzleDrupalHttp();
              // Function call to retrieve cities
              $response = $check->performRequest($endpoint_url,$request_method,$raw_headers,$payload_data);
              $contents = (string) $response->getBody();
              if($response->getStatusCode() == '200'){
                if($contents != ''){
                  //check for permission here
                  ///$result[$entity->id()]=$contents; //add all instances as a ARRAY (not need this at this time OK WORKS)
                  $result=$contents; //
                  
                  //print_r($contents);//ok WORKS
                }else{
                  print_r('200 '.$response->getReasonPhrase());
                }
              }
              else{
               // $result[$entity->id()] = $entity->title->value;
                ///$result[$entity->id()]=$contents; //add all instances as a ARRAY (not need this at this time OK WORKS)
                $result=$contents;
                
                //print_r("AAAAAAAAA"+$contents);//ok WORKS
               
                echo '</br>Response Status Code: ';
                print_r($response->getStatusCode());
                echo '</br>Reason Phrase: ';
                print_r($response->getReasonPhrase());
              }
              //die();

        };// END of  if($node->getType() == 'guzzle_rest'){
        

      } // END of foreach ($entities as $entity) {
      $response = new ResourceResponse($result);
      $response->addCacheableDependency($result);
      return $response;


    //return new ResourceResponse("Implement REST State GET!");
  }

}
