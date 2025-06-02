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

# php folder
check for php :
php --ini
if php is not present on local machine, download php files from php.net, save it in C: folder and locate php.ini
modified php.ini by uncommenting these lines with:
extension=mysqli
extension=openssl
extension=pdo_mysql
extension=mbstring
extension=intl

It will enable mySql, the encoding of characters and so on. Make sure the dll files are present in the ext folder.

## Pre-requirement:
Install composer on https://getcomposer.org/download/

install nelmio CORE bundle
install maker Bundle
composer require symfony/security-core

composer require symfony/security-bundle
composer require form validator
Now go to the RegistrationControllerand change the redirect after registration from _preview_error to app_login.

For the coordinates of the map:
composer require creof/doctrine2-spatial

## files modification
-> services.yaml (config\services.yaml)
-> .env (.env)
-> nelmio_cors.yaml (config\packages\nelmio_cors.yaml)
-> security.yaml (config\packages\security.yaml)

## add file:
(src/Security/LoginSuccesHandler.php) 

## launch in local
in cmd, write the command :
"symfony serve -no-tls"



