DC = docker compose
DC_RUN = ${DC} run --rm symfony_app
DC_EXEC = ${DC} exec symfony_app

init: down build install up success-message console ## Initialize environment

build: ## Build services.
	${DC} build $(c)

up: ## Create and start services.
	${DC} up -d $(c)

stop: ## Stop services.
	${DC} stop $(c)

start: ## Start services.
	${DC} start $(c)

down: ## Stop and remove containers and volumes.
	${DC} down -v $(c)

restart: stop start ## Restart services.

console: ## Login in console.
	${DC_EXEC} /bin/bash

install:
	${DC_RUN} composer install

success-message:
	@echo "Project initialized!"