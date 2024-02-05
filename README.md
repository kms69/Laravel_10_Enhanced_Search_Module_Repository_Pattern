
##### First step

docker compose up --build

##### Second step

 docker-compose exec app /bin/bash 
 
##### third step
  composer install 
  
  ##### fourth step
  php artisan migrate
  
   ##### fifth step
   php artisan db:seed

### Test
sixth step run the phpunit test command
`./vendor/bin/phpunit`



### running the code with one single command

`make run`

after that you can access the service with [http://localhost:8082](http://localhost:8082)

#### run tests 
`make test`