# API Symfony App #

### Description


## Attention to folders and files to be modified

-> config -> packages
        -> nelmio_cors.yaml
        -> security.yaml
-> routes
    -> services.yaml
    -> web_profiler.yaml

-> Controller
-> Entity
-> .env 
 

## Controller and Entity
Controler is used to 

## Pre-requirement:

install nelmio CORE bundle
install maker Bundle
composer require symfony/security-core

composer require symfony/security-bundle
composer require form validator
Now go to the RegistrationControllerand change the redirect after registration from _preview_error to app_login .

<!-- to generate a session token
composer require lexik/jwt-authentication-bundle --> not installed

For the coordinates of the map:
composer require creof/doctrine2-spatial

