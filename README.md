# API and Back Office Nation Sound #


# Table of content
-[📝Description](#description)

-[🔺Pre-requirement ](#pre-requirement)

-[🫗To load data in database](#to-load-data-in-database)

-[🚀Launch in local](#launch-in-local)

-[⚙️API ](#️api)

-[⚙️Back Office ](#️back-office)

-[⚙️Test](#️test)

-[🗺️Spatial data for the Poi Entity](#️spatial-data-for-the-poi-entity)

-[✏️Files modification](#️files-modification)

-[➕Add file](#add-file) 


# 📝Description 

The back end project for the responsive website of Nation Sound. An event of 3 days nearby Paris. This app will be used for back end and API purposes. Front user will fecth data from database and admin will run different operations to read, add, delete and update data in database through a user-friendly interface. 


#  🔺Pre-requirement 

Install composer on https://getcomposer.org/download 

install nelmio CORE bundle 

install maker Bundle 


## in CMD, write these commands: 

composer require symfony security-core 
composer require form validator 

## Now go to the 📂RegistrationController and change the redirect after resgistration from _preview_error to app_login 

📄Php.ini 

Check for php with command 

php --ini 

## If php is not there on your local machine, 
## download php files from php.net, save it in 📂C: folder and locate 📄php.ini. 
## You will then be able to modify by uncommenting these lines : 

extension=mysqli 

extension=openssl 

extension=pdo_mysql 

extension=mbstring 

extension=intl 

It will enable mySql, the encoding of characters and so on. Make sure the dll files are present in the 📂ext folder. 


# 🫗To load data in database 

## in cmd make : 

php bin/console make:migration 

php bin/console doctrine:migrations:migrate 

php bin/console doctrine:fixtures:load

yes 


# 🚀Launch in local 

## in cmd, write the command : 

symfony serve --no-tls 



# ⚙️API 

## We will now look for API uses of this app. 

### 🌴Tree structure 

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


# ⚙️Back Office 

## We will now look how the back office is structured 

### 🌴Tree structure 

apinewsymfony

├── config

│   ├── packages

│   │   ├── doctrine.yaml 

│   │   ├── nelmio_cors.yaml 

│   │   └── security.yaml 

│   └── services.yaml 

├── public

│   ├── assets

│   │   ├── css

│   │   │   └── app.css 

│   │   └── images 

│   │   └── JS 

│   └── index.php 

├── src

│   ├── Controller

│   │   └── BackOfficeController.php  

│   └── Security

│       └── LoginSuccesHandler.php 

├── templates

│   ├── Index

│   |   ├── adminConcerts.html.twig, addConcert.html.twig, addScene.html.twig, etc

│   ├── info 

│   ├── partners 

│   ├── Poi 

|   |── security 

|   base.html 

├── .env 

├── README.md 


# ⚙️Test 

## You will be able to find the unit test 

### 🌴Tree structure 

apinewsymfony

│   ├── DataFixtures

│   │   ├── AppFixtures.php 

│   │   └── data

│   │       └── liveEvent.sql 

├── tests/ 

│   ├── Entity

│   │   ├── UserTest.php 

│   │   ├── PoiTest.php 

├── phpunit.xml.dist 


## in cmd, write these commands : 

composer require --dev symfony/test-pack

composer require --dev doctrine/doctrine-fixtures-bundle

php bin/console --env=test doctrine:database:create

php bin/console --env=test doctrine:schema:create 

php bin/console --env=test 

Recommentdation: check 📂DataFixtures/AppFixtures.php 
copy paste code in notepad++ 
(Download app if needed on https://notepad-plus-plus.org/downloads/ ) 
And check if it is in🔣 UTF-8. Paste it back in 📄AppFixtures.php 


# 🗺️Spatial data for the Poi Entity 

## For the coordinates of the map: 

composer require creof/doctrine2-spatial 


# ✏️Files modification 

## These files need to be modified: 

📄services.yaml (📂config\services.yaml) 

📄.env (📂root) 

nelmio_cors.yaml (📂config\packages\nelmio_cors.yaml) 

security.yaml (📂config\packages\security.yaml) 

# ➕Add file 

📂src/Security/LoginSuccesHandler.php 

## 🎉🎉Installation and test is completed. Enjoy the app !!🎉🎉 ##

