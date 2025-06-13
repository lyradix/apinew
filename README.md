# API and Back Office Nation Sound # 

### Description

## Attention to folders and files to be modified

## For API uses
apinewsymfony/
├── config/
│   ├── packages/
│   │   ├── doctrine.yaml
│   │   ├── nelmio_cors.yaml
│   │   └── security.yaml
│   └── services.yaml
├── src/
│   ├── Controller/
│   │   └── IndexController.php
│   ├── DataFixtures/
│   │   ├── AppFixtures.php
│   │   └── data/
│   │       └── liveEvent.sql
│   ├── Entity/
│   │   └── User, Poi, Scene, Artist, Partners, Info
│   ├── Repository/
│   │   └── All related repositories
├── .env

## for Back Office 
apinewsymfony/
├── config/
│   ├── packages/
│   │   ├── doctrine.yaml
│   │   ├── nelmio_cors.yaml
│   │   └── security.yaml
│   └── services.yaml
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── app.css
│   │   └── images
│   │   └── JS
│   └── index.php
├── src/
│   ├── Controller/
│   │   └── BackOfficeController.php 
│   └── Security/
│       └── LoginSuccesHandler.php
├── templates/
│   ├── Index/
│   |   ├── adminConcerts.html.twig, addConcert.html.twig, addScene.html.twig, etc
│   ├── info
│   ├── partners
│   ├── Poi
|   |── security
|   base.html
├── .env
├── README.md

## Test

apinewsymfony/
│   ├── DataFixtures/
│   │   ├── AppFixtures.php
│   │   └── data/
│   │       └── liveEvent.sql
├── tests/
│   ├── Entity/
│   │   ├── UserTest.php
│   │   ├── PoiTest.php
├── phpunit.xml.dist

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

in cmd, write these commands :
composer require symfony/security-core

composer require symfony/security-bundle
composer require form validator
Now go to the RegistrationControllerand change the redirect after registration from _preview_error to app_login.

## Spatial data for the Poi Entity

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
"symfony serve --no-tls"

## For the Test
in cmd, write these commands :
composer require --dev symfony/test-pack
php bin/phpunit
composer require --dev doctrine/doctrine-fixtures-bundle

Recommentdation: check DataFixtures/AppFixtures.php
copy paste code in notepad++ (Download app if needed on https://notepad-plus-plus.org/downloads/ )
And check if it is in UTF-8. Paste it back in AppFixtures.php






