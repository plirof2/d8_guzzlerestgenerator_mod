# guzzle rest endpoint

## Description:
This module repeats/relays an external API through the internal Drupal REST authentication/authorisation mechanism.

This is an extension to guzzlerestgenerator . It adds REST support to each guzzle_node. This exposes any external api defined in guzzle_node as a drupal8 REST point. Also, node_view_permissions module is also recommended.

## Installation NOTE:
At this point you must manually enable guzzlenode_user_api_access and  guzzlenode_role_api_access FIELDS from 
http://mydrupalsite/admin/structure/types/manage/guzzle_rest/form-display


## Usage administration-side: 
First you enable from REST-UI guzzle node
and set permissions/auth types etc
Add a guzzle_node (just like adding content) .
There you define:
- external API , 
- headers (eg APIkey etc.) 
- Body (in case of POST/PUT)



Then you need to define access.

## Usage external-client-side: 
You can connect with urls like :

http://mydrupal.com/api/relay/getalbums?_format=hal_json&param1=Ubuntu&param2=Music

this will relay your request as 

http://myjsonserver/albums/Ubuntu/Music




To Do: 
- Implement POST
- Implement PUT
- Implement access per SPECIFIC guzzle_node (per role or user)


