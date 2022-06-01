### How to build the project
In docker folder create .env file from .env.dist

In .env file set APP_PATH for application root

In project folder run next command to build docker php containers:

    make build


To run containers:

    make up


To go inside php container run:

    make exec-php

Inside container run

    composer install

Application is available at http://10.5.0.1

Documentation: http://10.5.0.1/documentation/api

### How to generate the certificate for jwt
    
    php artisan jwt:secret
    php artisan jwt:generate-certs --force --algo=rsa --bits=4096 --sha=512
