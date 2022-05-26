build:
	@cd docker && docker-compose build

rebuild:
	@cd docker && docker-compose build --no-cache

up:
	@cd docker && docker-compose up -d

stop:
	@cd docker && docker-compose stop

exec-php:
	@cd docker && docker-compose exec php_api-example-laravel /bin/bash
