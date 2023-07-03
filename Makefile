SHELL=/bin/bash

ifeq ($(OS), Windows_NT)
OS_NAME="Windows"
else
UNAME=$(shell uname)
ifeq ($(UNAME),Linux)
OS_NAME="Linux"
else
ifeq ($(UNAME),Darwin)
OS_NAME="MacOS"
else
OS_NAME="Other"
endif
endif
endif

install:
	make build
	cp .env.example .env
	make up
	docker-compose exec app composer install
	docker-compose exec app npm install
	docker-compose exec app php artisan key:generate
	sudo chmod -fR 777 storage bootstrap

build:
	docker-compose build

up:
	USER_NAME=$(shell id -nu) USER_ID=$(shell id -u) GROUP_NAME=$(shell id -ng) GROUP_ID=$(shell id -g) OS_NAME=$(OS_NAME) docker-compose up -d

stop:
	docker-compose stop

down:
	docker-compose down

docker-build:
	docker-compose build

ps:
	docker-compose ps

# MySQLコンテナexec
mysql:
	docker exec -it laravel-setup-practice-mysql /bin/bash -c "mysql -uroot -ppassword"

# Redisコンテナexec
redis:
	docker exec -it laravel-setup-practice-redis /bin/bash -c "redis-cli"

# コンテナ再起動
restart:
.PHONY: restart
ifeq (restart,$(firstword $(MAKECMDGOALS)))
  RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(RUN_ARGS):;@:)
endif
restart: ## Restart services : ## make restart, make restart app
	docker-compose -f docker-compose.yml kill $(RUN_ARGS) && \
	docker-compose -f docker-compose.yml rm -f $(RUN_ARGS) && \
	docker-compose -f docker-compose.yml up -d $(RUN_ARGS)

# ログ
logs:
.PHONY: logs
ifeq (logs,$(firstword $(MAKECMDGOALS)))
  RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(RUN_ARGS):;@:)
endif
logs: ## Restart services : ## make restart, make restart app
	docker-compose -f docker-compose.yml logs $(RUN_ARGS)

# kill
.PHONY: kill
ifeq (kill,$(firstword $(MAKECMDGOALS)))
  RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(RUN_ARGS):;@:)
endif
kill: ## kill containers : ## make kill app
	docker-compose -f docker-compose.yml kill $(RUN_ARGS)

# tinker
tinker:
.PHONY: tinker
ifeq (tinker,$(firstword $(MAKECMDGOALS)))
  RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  $(eval $(RUN_ARGS):;@:)
endif
tinker: ## Restart services : ## make restart, make restart app
	docker-compose -f docker-compose.yml exec app /bin/sh -c "php artisan tinker"

format:
	docker-compose exec app ./vendor/bin/pint

analysis:
	docker-compose exec app ./vendor/bin/phpstan analyse --error-format=checkstyle --memory-limit=-1

ifeq ($(OS_NAME), "Linux")
shell:
	docker exec -it laravel-setup-practice-app su -s /bin/bash $(shell id -un)
else
shell:
	docker exec -it laravel-setup-practice-app /bin/bash
endif

dev:
	docker compose exec app npm run dev

ide-helper:
	php artisan ide-helper:generate
	php artisan ide-helper:models --nowrite
	php artisan ide-helper:meta

cache-clear:
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

fresh:
	docker-compose exec app php artisan migrate:fresh --seed

api-generate:
	php artisan openapi:generate > ./dist/openapi.json
