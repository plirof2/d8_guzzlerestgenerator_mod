#guzzle endpoint

# d8_jon_custom_rest
d8_jon_custom_rest  - Test custom REST for external_entities module



get method:
loads extent001 entity
```
$entities = \Drupal::entityTypeManager()->getStorage('external_entity_type')->loadByProperties(['type' => 'extent001']); // get specific entity
return new ResourceResponse($entities);
````


list all bundles 
```
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