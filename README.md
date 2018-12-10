# Test random number queue application using RabbitMQ

[![Build Status](https://travis-ci.org/gtgt/randomqueue.svg?branch=master&style=flat-square)](https://travis-ci.org/gtgt/randomqueue)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](LICENSE)

## Software used

* [nginx](https://nginx.org/)
* [PHP-FPM](https://php-fpm.org/)
* [RabbitMQ](https://www.rabbitmq.com/)
* [MySQL](https://www.mysql.com/)

## Requirements

This stack needs [docker](https://www.docker.com/) and [docker-compose](https://docs.docker.com/compose/) to be installed.

## Installation

1. Create a `.env` file from `.env.dist` and adapt it according to the needs of the application

    ```sh
    $ cp .env.dist .env && nano .env
    ```
2. Build and run the stack in detached mode (stop any system's ngixn/apache2 service first)

    ```sh
    $ docker-compose build
    $ docker-compose up -d # or without -d if you are debugging 
    ```

3. Get the bridge IP address

    ```sh
    $ docker network inspect bridge | grep Gateway | grep -o -E '[0-9\.]+'
    # OR an alternative command
    $ ifconfig docker0 | awk '/inet:/{ print substr($2,6); exit }'
    ```

4. Update your system's hosts file with the IP retrieved in **step 3**

5. Prepare the Symfony application
    1. Update Symfony env variables (*.env*)

        ```
        #...
        DATABASE_URL=mysql://db_user:db_password@mysql:3366/db_name
        #...
        ```

    2. Composer install & update the schema from the container

        ```sh
        $ docker-compose exec php bash
        $ composer install
        ```
6. (Optional) Xdebug: Configure your IDE to connect to port `9001` with key `PHPSTORM` (for web-interface)

## How does it work?

We have the following *docker-compose* built images:

* `consumer`: The consumer container which starts the consumer when up.
* `nginx`: The Nginx webserver container for accessing web interface.
* `php-fpm`: The PHP-FPM container to run php for the web interface.
* `mysql`: The MySQL database container.
* `rabbit`: The RabbitMQ server/administration container.

Running `docker-compose ps` should result in the following running containers:

```
           Name                          Command               State              Ports
--------------------------------------------------------------------------------------------------
randomqueue_mysql         /entrypoint.sh mysqld            Up      0.0.0.0:3366->3366/tcp
randomqueue_nginx         nginx                            Up      443/tcp, 0.0.0.0:80->80/tcp
randomqueue_php-fpm       php-fpm                          Up      0.0.0.0:9000->9000/tcp
randomqueue_rabbit        rabbitmq:3-management            Up      4369/tcp, 5671/tcp, 0.0.0.0:5672->5672/tcp, 15671/tcp, 25672/tcp, 0.0.0.0:15672->15672
```

## Usage

Once all the containers are up, our services are available at:

* Symfony app: `http://localhost:8080`
* Mysql server: `127.0.0.1:3366`
* RabbitMQ: `http://localhost:15672`
* Log files location: `logs/*`
---

:tada: Now we can stop our stack with `docker-compose down` and start it again with `docker-compose up -d`
