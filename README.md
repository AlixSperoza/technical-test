## Installation instructions

 - Check the minimum technical requirements :
    - PHP 8.2
    - NodeJS 20.0.0
    - MySQL or MariaDB

 - Create your SQL user and your database
 - Import your MQTT messages SQL file into your database

 - Launch the commands :
   - git clone git@github.com:AlixSperoza/technical-test.git
   - cd technical-test
   - cp .env.example .env
   - composer install
   - npm install
   - php artisan migrate
   - php artisan regroup:mqtt_messages

 - In two different consoles, in the project folder :
   - php artisan serve
   - npm run dev

You can access to http://localhost:8000 for the view of the second step.

## Technical test

I used 10 hours to understand and to make the technical test.

This test seems good to check how unplanned cases are handled.
For example, one of the SQL record has an incorrect JSON format.