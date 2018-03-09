<?php

namespace Drupal\d8_guzzlenode_rest\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Psr\Log\LoggerInterface;

use Drupal\guzzlerestgenerator\Http\GuzzleDrupalHttp; // DEPENDS on guzzlerestgenerator module
use Symfony\Component\HttpFoundation\Request;
//*     "https://www.drupal.org/link-relations/create" = "/api/relay/post/{nid}"

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "guzzlenode_rest_resource",
 *   label = @Translation("Guzzlenode rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/relay/{nid}",
 *     "https://www.drupal.org/link-relations/create" = "/api/relay/post/{nid}"
 *
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


  //protected $nid=0;
  //protected $arg1=0;
  //protected $arg2=0;

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
  //public function get() { //ORIGINAL
  public function get($nid=0,$data) {    

    return $this->do_request($nid,$data,"GET");
    //return new ResourceResponse("Implement REST State GET!");
  }

  protected function is_in_subarray($val, $entity_get_values){
		$entity_get_values=array_filter($entity_get_values);
		if(empty($entity_get_values)) return false;
		foreach($entity_get_values as $one_value){
			if($val==$one_value['target_id']) return true;
		}
		return false;
  } //end of function is_in_subarray($val, $entity_get_values){

  protected function is_in_subarray2($val, $entity_get_values){
  		
		$entity_get_values=array_filter($entity_get_values);
		if(empty($entity_get_values)) return false;
		$encoded=json_encode($entity_get_values,true); //[{"target_id":3714},{"target_id":1114},{"target_id":1082}]
		$exploded = preg_split( '/(},{"target_id":|{"target_id":|}]|\[|\")| /', $encoded );
		//var_dump(array_intersect($val, $exploded));
		\Drupal::logger('is_in_subarray2')->notice("val encoded=".json_encode($val,true)." , encoded=$encoded  || exploded =".json_encode($exploded,true) );	
		if (array_intersect($val, $exploded)) \Drupal::logger('INTERSECT is_in_subarray2 check OK')->notice("val=".json_encode($val,true)." IS INSIDE encoded=$encoded  ||");
		//if (in_array($val, $exploded,true)) return true;
		if (array_intersect($val, $exploded)) return true;
		return false;
  } //end of function is_in_subarray($val, $entity_get_values){



    /**
     * Responds to POST requests.
     * @return \Drupal\rest\ResourceResponse
     * Returns a list of bundles for specified entity.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function post($nid=0,$data) {
      \Drupal::logger('guzzlenodeResr POST ok')->notice("ok POST nid=".$nid ." || DATA=".json_encode($data,true));  
      return $this->do_request($nid,$data,"POST");
      //  \Drupal::logger('guzzlenodeResr PATCH ok')->notice("ok PATCH nid=".$nid ." || DATA=".json_encode($data,true));  
        /*
        $response = array(
            "data" => $data,
        );

        $build = array(
            '#cache' => array(
                'max-age' => 0,
            ),
        );
    */
        //$response= new ResourceResponse(array (0=>"hello POST nid=$nid",$data));
        //return $response;
        //return (new ResourceResponse($response))->addCacheableDependency($build);
    }

    /**
     * Responds to POST requests.
     * @return \Drupal\rest\ResourceResponse
     * Returns a list of bundles for specified entity.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function patch($nid=0,$data) {
        $this->do_request($nid,$data,"PATCH");
        \Drupal::logger('guzzlenodeResr PATCH ok')->notice("ok PATCH nid=".$nid ." || DATA=".json_encode($data,true));  
        /*
        $response = array(
            "data" => $data,
        );

        $build = array(
            '#cache' => array(
                'max-age' => 0,
            ),
        );
    */
        $response= new ResourceResponse(array (0=>"hello PATCH nid=$nid",$data));
        return $response;
        return (new ResourceResponse($response))->addCacheableDependency($build);
    } // END of public function patch($nid=0,$data) {






    /**
     * Responds to GET,POST,PATCH requests. All the above call this (since we have common handling)
     * @return \Drupal\rest\ResourceResponse
     * Returns a list of bundles for specified entity.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */      
    protected function do_request($nid,$data=null,$method="GET")      {
        //PUT common code here 
        \Drupal::logger('guzzlenodeRest DO_REQUEST ok')->notice("ok $method nid=".$nid ." || DATA=".json_encode($data,true));  



    //########## SECURITY CHECK Level #1 : user must have a general permission to view NODES 
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    //Should Put the following CODE to the NORMAL permission check @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    if (!$this->currentUser->hasPermission('guzzlenode allow access to ext API guzzlenodes')) {
      \Drupal::logger('guzzlenode_rest ACCESS debug')->notice("NOOOO  we DO NOT have guzzlenode allow access to ext API guzzlenodes PERMISSION");
    } else {      \Drupal::logger('guzzlenode_rest ACCESS debug')->notice("OK we HAVE guzzlenode allow access to ext API guzzlenodes PERMISSION"); }   

      //$entities = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple(); //ORIG WORKED

      $entities = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties(['type' => 'guzzle_rest']); //LOAD ONLY guzzle nodes
        //->loadMultiple();
         //$entities = \Drupal::entityTypeManager()->getStorage('external_entity_type')->loadByProperties(['type' => 'extent001']); // get specific entity JON TEST


       /*
      // Params note (for URL like drupal.com?params[hello]=geiasou&params[name]=George :        
    foreach ($idArray as $index => $avPair)
    {
      list($ignore, $value) = explode("=", $avPair);
      $id[$index] = $value;
    }

       */


      //$get_node_id = $this->currentRequest->get('id1'); //get URL REST node id drupal.com/restapi/?myname=john  
      /*  GET patameters NOT implemented yet (maybe I need to define $param1_value , $param1_name in drupal URL)
          // parameters are SEND to the  external request URLs as GET : http://externalapi.com?param1=hello&param2=world
          $get_param1_name = $this->currentRequest->get('param1_name'); //get URL REST node id drupal.com/restapi/?myname=john  
          $get_param2_name = $this->currentRequest->get('param2_name'); //get URL REST node id drupal.com/restapi/?myname=john
          $get_param1_value = $this->currentRequest->get('param1_value'); //get URL REST node id drupal.com/restapi/?myname=john  
          $get_param2_value = $this->currentRequest->get('param2_value'); //get URL REST node id drupal.com/restapi/?myname=john              
      */

      //$node_id_from_arg = $nid;
      //$node_id_from_alias=\Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$nid); //GET path alias from node_ideg drupal.com/restapi/myalbum
      //Todo Do some chech if $nid is number
      $node_id_from_alias="0";
      //if ($nid!=0) 
      $node_id_from_alias=\Drupal::service('path.alias_manager')->getPathByAlias('/'.$nid); //GET node_id from path alias drupal.com/restapi/myalbum
      $node_id_from_alias=str_replace("/node/", "", $node_id_from_alias);


      \Drupal::logger('DEBUG guzzlenode_rest')->notice("Args: nid=$nid ,node_id_from_alias = $node_id_from_alias , arg1=$get_arg1, arg2=$get_arg2 ,get_relay_headers=$get_relay_headers "); //DEBUG

      // JON NOTE : BUG at this moment it shows ONLY 1 guzzlenode (even if you have more)  
      foreach ($entities as $entity) {
        // $result[$entity->id()] = $entity->title->value; // OK WORKS returns all titles
        
        //if(($entity->getType() == 'guzzle_rest') && ( $entity->id()==$get_node_id) ){  //ok works Example with URL like drupal.com/restapi/?myname=john
      if( ( $entity->id()==$nid) || ( $entity->id()==$node_id_from_alias) ){   // test URLs like drupal.com/restapi/4 or drupal.com/restapi/myalbum
     
        // NOW we check extra permissions: @@@@ --------------------------------------------------------------
         $current_user_roles=$this->currentUser->getRoles();
         //get status, allowed users and allowed roles lists :
         $guzzlenode_user_api_access = $entity->get('field_guzzlenode_user_api_access')->getValue();
         $guzzlenode_role_api_access = $entity->get('field_guzzlenode_role_api_access')->getValue();
         $status = $entity->get('status')->value; //JON is this guzzlenode published; 


         \Drupal::logger('GuzzleNodeRestResource USER_API_ACCESS')->notice("NODE STATUS= $status ||| current user=".json_encode($this->currentUser->id())."  ||| guzzlenode_user_api_access=".json_encode($guzzlenode_user_api_access,true)."||| guzzlenode_ROLE_api_access=".json_encode($guzzlenode_role_api_access,true));  

         if (!$status) {
            //throw new UnauthorizedHttpException();
            throw new UnauthorizedHttpException((string) $challenge, 'Error 4532 - Resource disabled or unpublished', $previous);
         }

         //if current user is NOT in the allowed users then STOP HIM
         if (!(
          ($this->is_in_subarray($this->currentUser->id(),  $guzzlenode_user_api_access)) 
          || ($this->currentUser->id()=="1")
          || ($this->is_in_subarray2($current_user_roles,  $guzzlenode_role_api_access)) 
          ))
         {
          \Drupal::logger('GuzzleNodeRestResource 1')->notice("Error 4732 - USER NOT ALLOWED");
           //throw new UnauthorizedHttpException();
           throw new UnauthorizedHttpException((string) $challenge, 'Error 4732 - Resource unavailable/User Access Denied', $previous);
         }
       // END of  NOW we check extra permissions: @@@@ ________________________________________________________

         // Ok Access is allowed. Now we proceed and get the rest settings:
         //$gnode_allow_arg_fwd= $entity->get('field_gnode_allow_arg_fwd')->getValue()[0]['value'];
         $gnode_allow_arg_fwd= $entity->get('field_gnode_allow_arg_fwd')->value;
         $gnode_allow_head_fwd= $entity->get('field_gnode_allow_head_fwd')->value;
         $gnode_allow_param_fwd= $entity->get('field_gnode_allow_param_fwd')->value;
         $gnode_allow_payload_fwd= $entity->get('field_gnode_allow_payload_fwd')->value;
         $gnode_call_limit= $entity->get('field_guzzlenode_call_limit')->value;

         \Drupal::logger('GuzzleNodeVARIABLES')->notice("gnode_allow_arg_fwd=".$gnode_allow_arg_fwd . "||gnode_allow_head_fwd=".json_encode($gnode_allow_head_fwd,true) ); 


      //From here on we think all are allowed  

         //$result[$entity->id()] = $entity->title->value;
              // Retrieve node fields to perform REST request
         $endpoint_url = $entity->get('field_guzzle_endpoint_url')->uri;
         $request_method = $entity->get('field_guzzle_request_method')->value;
         $raw_headers = $entity->get('field_guzzle_raw_headers')->value;
         $payload_data = $entity->get('field_guzzle_data_payload')->value;
         


         // Check if Arg Forward is selected
         if ($gnode_allow_arg_fwd=="1"){
            // Arguments are SEND to the client in this format : http://externalapi.com/arg1/arg2
            //get URL REST relay headers to the next request ? (this is used for requesting a drupal api FROM INSIDE drupal API - kind like subreqiests)
			$args_array=$this->currentRequest->get('arg');
			$count_args=count($args_array);
			if($count_args>0 ){
				if ($count_args>5) $count_args=5; // we check the first 5 arga are arg[1],arg[2]...arg[5]
				for ($i=1;$i<=5;$i++){
					if (isset($args_array[$i]))
					{
						$endpoint_url=$endpoint_url.'/'.$args_array[$i]; // ******** Might need to sanitaze
					}
				}
				$endpoint_url=$endpoint_url.'/'; //Add a trailing slash to the end of the external URL <-----
			\Drupal::logger('endpoint_url guzzlenode_rest ok GET ARGS')->notice(" endpoint_url=$endpoint_url");
			}// END of if($count_args>0 ){                 
          } // END OF if ($gnode_allow_arg_fwd=="1"){

         // Check for headers foowrward . Forward "Authorization:"" header to External Request"
         if ($gnode_allow_head_fwd=="1"){
             //$get_relay_headers = $this->currentRequest->get('relay_headers'); 
             $get_relay_headers ='yes';
             if ($get_relay_headers=='yes'){
                \Drupal::logger('raw_headers BEFORE DEBUG guzzlenode_rest URI')->notice("raw_headers=$raw_headers");
                //Relay Authorization Header... NOTE to NOT overwrite ORiginal
                //ToDO: ralay ALL headers to next request @@@@
                ///$currentRequestAuthorizationHeader=$this->currentRequest->headers->get( 'Authorization' );
                $currentRequestAuthorizationHeader=$this->currentRequest->headers->get( 'Authorization2' );
                if ($currentRequestAuthorizationHeader!=null) {
                    $raw_headers=$raw_headers."\r\n".'Authorization:'.$currentRequestAuthorizationHeader;
                    //\Drupal::logger('raw_headers AFTER DEBUG guzzlenode_rest URI')->notice("raw_headers=$raw_headers"); 
                }
              } // END of if ($get_relay_headers=='yes'){          
          } // END OF if ($gnode_allow_head_fwd=="1"){


         // Check if forwarding POST/PAYLOAD to external API is checked
        if ($method=="POST" && $gnode_allow_payload_fwd=="1"){
            //$payload_to_forward = $entity->get('field_guzzle_data_payload')->value;
          // NOTE @@@@@@@@@@  payload_data handling must be fixed in ORIGINAL 
          //\Drupal::logger('DEBUG guzzlenode_rest ok POST FWD')->notice(" payload_BEFORE=".json_encode($payload_data,true)); 
          $payload_data=json_encode($data,true);
          //\Drupal::logger('DEBUG guzzlenode_rest ok POST FWD')->notice(" payload_ENCODED=".json_encode($payload_data,true));
          //  \Drupal::logger('DEBUG guzzlenode_rest ok POST FWD')->notice(" data ENC=".json_encode($data,true)); 
        } // END Of  if ($gnode_allow_param_fwd=="1"){


         // Check if PARAM Forward (as GET url encoded)is selected // params  (might need to sanitize)
         if ($gnode_allow_param_fwd=="1"){

			$param_names_array=$this->currentRequest->get('param_name');
			$param_values_array=$this->currentRequest->get('param_value');
			$count_params=count($param_names_array);
			$get_external_request_params_array=array();
			if($count_params>0 ){
				if ($count_params>5) $count_params=5; // we check ONLY the first 5 
				for ($i=1;$i<=5;$i++){
					if (isset($param_names_array[$i]) && isset($param_values_array[$i] ))
					{
						$get_external_request_params_array [$param_names_array[$i]]= $param_values_array[$i];
						//$endpoint_url=$endpoint_url.'/'.$param_names_array[$i]; // ******** Might need to sanitaze
					}
				} //end of for ($i=1;$i<=5;$i++){
				$get_tail=http_build_query($get_external_request_params_array);
                $pos = strpos($endpoint_url, '?'); //check if our external API URL already contains '?' Note: This check might NOT be needed
                  // Note our use of ===.  Simply == would not work as expected
                  // because the position of 'a' was the 0th (first) character.
                if ($pos === false) { 
                      $endpoint_url=$endpoint_url.'?'.$get_tail;
                  } else {
                      $endpoint_url=$endpoint_url.'&'.$get_tail;
                }				

			\Drupal::logger('PARAM endpoint_url guzzlenode_rest ok GET ARGS')->notice(" endpoint_url=$endpoint_url   ");
			}// END of if($count_args>0 ){
         } //END OF if ($gnode_allow_param_fwd=="1"){     



         \Drupal::logger('DEBUG guzzlenode_rest URI')->notice(" endpoint_url=$endpoint_url ,raw_headers=$raw_headers");
         //ALL 
         // Perform GuzzleDrupalHttp request
         $check = new GuzzleDrupalHttp();
         // Function call to retrieve cities
         $response = $check->performRequest($endpoint_url,$request_method,$raw_headers,$payload_data);
         $contents = (string) $response->getBody();
         if($response->getStatusCode() == '200'){
             if($contents != ''){
               //check for permission here
               ///$result[$entity->id()]=$contents; //add all instances as a ARRAY (not need this at this time OK WORKS) // solution #3 (escaped chars)
               $result=$contents;// ok WORKS // solution #1 (escaped chars) 
               //print_r($contents);//ok WORKS // solution #2
             }else{
               print_r('200 '.$response->getReasonPhrase());
             }
         }
         else{
             // $result[$entity->id()] = $entity->title->value;
             ///$result[$entity->id()]=$contents; //add all instances as a ARRAY (not need this at this time OK WORKS)// solution #3 (escaped chars)
             $result=$contents; // ok WORKS // solution #1 (escaped chars) 
                
             //print_r($contents);//ok WORKS // solution #2
               
            echo '</br>Response Status Code: ';
            print_r($response->getStatusCode());
            echo '</br>Reason Phrase: ';
            print_r($response->getReasonPhrase());
         } // END of   if($response->getStatusCode() == '200'){

              //die();  // ### NOTE: This assumes that we only want 1 guzzlenode entity each time (this mshould be correct - solution #3 will not work with die() )

      };// END of  if($node->getType() == 'guzzle_rest'){
        

      } // END of foreach ($entities as $entity) {

      $result=json_decode($result, true); //added by Jon 180224 for clean responces
      $response = new ResourceResponse($result);
      $response->addCacheableDependency($result);
      return $response;




//        $response= new ResourceResponse(array (0=>"do_request Hello nid=$nid",$data));
//        return $response;
    }//protected function do_request()      {


}
