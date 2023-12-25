Order fileservice
=========
###Get repository
* git clone 
###Steps to run in docker
* create .env file with the proper configuration data 
* run "docker-compose up --build"
* run in browser phpMyAdmin "localhost:8080", log in and create a database
* run bash in docker container "docker exec -it fs_php bash"
* install dependencies "composer install"
* create db structure "bin/console d:s:u --force"
* check http://localhost:8741
###Create a user 
* goto ApiLoginController, uncomment "register" method 
* use it to create a user (POST http://localhost:8741/api/register body: {"email": "order@utr.ua","password": "order123"})
* log in and obtain token (POST http://localhost:8741/api/login body: {"username": "order@utr.ua","password": "order123"})
* use token in request headers "Authorization: Bearer {{token}}"


