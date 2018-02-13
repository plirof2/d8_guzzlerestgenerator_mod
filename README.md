#guzzle endpoint

# d8_jon_custom_rest
d8_jon_custom_rest  - Test custom REST for external_entities module



###get method:
loads extent001 entity
```php
$entities = \Drupal::entityTypeManager()->getStorage('external_entity_type')->loadByProperties(['type' => 'extent001']); // get specific entity
return new ResourceResponse($entities);
````


###list all bundles 
```php
 $bundles_entities = \Drupal::entityManager()->getStorage('external_entity' .'_type')->loadMultiple();
 //$bundles_entities = \Drupal::entityManager()->getStorage('extent001' .'_type')->loadMultiple();
      $bundles = array();
      foreach ($bundles_entities as $entity) {
        $bundles[$entity->id()] = $entity->label();
        if($entity->label() == 'extent001'){
          return new ResourceResponse("OK found extent001  Implement REST State GET! ");
        }

      }
   if (!empty($bundles)) {
        return new ResourceResponse($bundles);
      }    
```




###REST call default (d8_guzzlenode_rest)
```php

http://localhost/drupal8test/api/relay/?_format=hal_json

The initial (default) module returns:
{
    "1": "Intro Page",
    "2": "article1",
    "3": "jsonplaceholder.typicode.com/todos node3"
}

```
http://valuebound.com/resources/blog/create-rest-resource-for-get-method-drupal-8

### Get URL parameter of drupal REST request "canonical" = "/content/get/{id}"
```php
public function get() {
  ...
$myname = $this->currentRequest->get('myname'); // drupal.com/restapi/?myname=john
  ...
}  
```
https://drupal.stackexchange.com/questions/213184/how-i-can-add-an-optional-parameter-in-rest-api-url


