# guzzle rest endpoint

## Description:
This module repeats/relays an external API through the internal Drupal REST authentication/authorisation mechanism.

This is an extension to guzzlerestgenerator . It adds REST support to each guzzle_node. This exposes any external api defined in guzzle_node as a drupal8 REST point. Also, node_view_permissions module is also recommended.
Note : for relay_headers to work you need the modified guzzlerestgenerator module.

## Installation NOTE:
At this point you must manually enable guzzlenode_user_api_access and  guzzlenode_role_api_access FIELDS from 
http://mydrupalsite/admin/structure/types/manage/guzzle_rest/form-display

### Access rights:
This is a general permission to be able to access ALL guzzlenodes/external APIs (but not use them - you will need to specify usernames/roles inside EACH guzzlenode). Note 1: this ENABLES/DISABLES everything for everyone.So, you MUST have this permission in order to do anything. Note 2: From "RESTful Web Services" you can further control access to Drupal GET,POST,PATCH REST points. The final check is done when you check if the current USERNAME is declared in the specific guzzlenode

- Give permissions /admin/people/permissions to Drupal REST -> from here allow GET,POST,PATCH REST access to this drupal guzzlenode
- Give permissions /admin/people/permissions to All GuzzleNodes (initial access) -> **d8_guzzlenode_rest->GuzzleREST - General access to external guzzleAPIs** :  This gives general access to external guzzleAPIs .(machine name: guzzlenode allow access to ext API guzzlenodes)
- by default all REST GET,POST are already activated from here : /admin/config/services/rest/resource/guzzlenode_rest_resource/edit
- 

Notes:
ERRORS:
 Error 4732 = This means that current username is NOT defined inside guzzlenode so we can allow him to access this resource.
 Error 4532 = Resource disabled. This resource is unpublished (the specific guzzlenode is saved as unpublished).


## Usage administration-side: 
First you enable from REST-UI guzzle node
and set permissions/auth types etc
Add a guzzle_node (just like adding content) .
There you define:
- external API , 
- headers (eg APIkey etc.) 
- Body (in case of POST/PUT)

Then you need to define access.


## Usage external-client-side #1: 
You can connect with urls like :

**http://mydrupal.com/api/relay/getalbums?_format=hal_json&param1=Ubuntu&param2=Music**

this will relay/forward your request as :

**http://myjsonserver/albums/Ubuntu/Music**


## Usage external-client-side #2: 

### Forward POST
If you POST to the 
**http://mydrupal.com/api/relay/post/3**
and you have POST/Payload body forward enabled then, the body you have post will be forwarded to the external API call.

### Forward Headers
If you have enabled this :
If you post a header named 
**authorisation2:...**
then it will be forwarded as a standard
**authorisation:..**
header to the external API

### Args :
**http://mydrupal.com/api/relay/getalbums?_format=json&arg[2]=world&arg[1]=hello**
will send to the external API a request like

**http://myexternalserver/hello/world**
At this point we accept up to 5 args : arg[1]...arg[5].

### Parameters :
Pass parameters to External API url 
eg 
 **http://mydrupal.com/api/relay/3?_format=json&param_name[1]=hello&param_value[1]=55555555&param_name[2]=world&param_value[2]=44444**
Will produce :
**http://myexternalserver?hello=55555555&world=44444**

At this point we accept up to 5 params : param_name[1],param_value[1]...param_name[5],param_value[5]

## To Do: 
- Allow FIXED payload to accept variables from drupal rest call API point
- replace use Drupal\guzzlerestgenerator\Http\GuzzleDrupalHttp with a local one
- Implement PUT
- Implement access per SPECIFIC guzzle_node (per role or user)


