## SIMPLE INSTA APPLICATION

#### Features
- Register
- Login
- Create Post
- Read Post / Timeline
- Like & Comment

#### Quick Start
##### One time setup in development
- create .env file copy from .env-example, please change postgres connection using your postgres
- create database with name instaapp_db
- run migration script ```php migrate.php```
- create folder under ```/public``` to save posting image
- adjust .env ```IMAGE_PATH``` will contains ```/public```
- adjust .env ```IMAGE_URL``` your posting image folder name
- create .env file under ```frontend/instaapp``` copy from ```.env-example```
- install node dependencies by ```cd frontend/instaapp``` and ```npm install```

##### Running Backend Service
- run php server ```php -S 0.0.0.0:9099 -t public```

##### Running Frontend Service
- ```cd frontend/instaapp``` and ```npm run dev```

