## About Property-Stat

Property-Stat is a web application (api endpoint) developed in Laravel to address calculating min, max, median on arbitrary defined attributes
(analytics) on a database of realestate properties.

## How to install

After cloning the repo and changing directory to root:
### framework:
 Run fallowing command on the console:
 
 composer install

###database:

1- Create an empty Mysql database

2- Make sure database credentials and database name are correctly set in .env file

3- To populate the schemas run:

   php artisan migrate
   

4- To populate few sample data run sql scripts:

   ./database/sample-data/archistar_db_properties.sql
   
   ./database/sample-data/archistar_db_analytic_types.sql
   
   ./database/sample-data/archistar_db_property_analytics.sql
   
## How to run
 Run a php web server such as:
 
 php -S localhost:8000 -t public
 
 
 For smoke tesing open your browser and on the browser address bar run :
 
  http://localhost:8000
  
  
 And click on the provided list on the home page to initiate a sample api end point get request 
 (note: for simplicity authentication is not implemented)
 
## How to test

1- for command line visual verification testing run

For calculating statistics on country level example:

  php artisan DB:Q  "{'action': 'stats', 'region' : 'AU'}"
  
On state level:

  php artisan DB:Q  "{'action': 'stats', 'region' : 'AU,NSW'}"  

On suburb level:

  php artisan DB:Q  "{'action': 'stats', 'region' : 'AU,NSW,Ingleburn'}"  

2- For phpunit testing  simply run:

 composer test
 
 (First make sure the phpunit base directory is correct in composer.json)

## Utility commands

- listing all existing properties:

php artisan DB:Q  "{'action': 'list', 'subject': 'Property'}"

- listing all existing analytic types 

php artisan DB:Q  "{'action': 'list', 'subject': 'AnalyticType'}"

- listing all properties anlytic values

php artisan DB:Q  "{'action': 'list', 'subject': 'PropertyAnalytic'}"

- updating (or adding) an analytic value for a property (example)

php artisan DB:Q  "{'action': 'update', 'subject': 'PropertyAnalytic', 'prop_guid' : '1', 'analytic_name': 'Price', 'value': 650000}"


## api endpoints examples

- calculate statistic over a country, a state or a suburb:

http://localhost:8000/api/v1/stats/Au

http://localhost:8000/api/v1/stats/Au,NSW

http://localhost:8000/api/v1/stats/Au,NSW,Ingleburn

- add\update a property

http://localhost:8000/api/v1/update/property?guid=10&country=UK&state=Oxford&suburb=Burmingham

- add\update an AnalyticType

http://localhost:8000/api/v1/update/analytic_type?name=land_tax&units=AUD&is_numeric=1&num_decimal_places=2

- assign\update an existing AnalyticType value to a property

http://localhost:8000/api/v1/update/property_analytic?prop_guid=10&analytic_name=Price&value=850000


## License

Property-Stat is licensed under the [MIT license](https://opensource.org/licenses/MIT).
code reuse/fork is fine when referring the author's name in your projects.
 
Author:
Maziar Navabi
15/06/2020

