 .PHONY: build install test run

# Build the development container
build:
	docker build -t php-cli-dev -f Dockerfile .

# Install dependencies
install:
	docker run --rm -v $(PWD):/app php-cli-dev composer install

# Get a shell in the container
shell:
	docker compose run --rm app bash

# Run tests
test:
	docker compose run --rm app vendor/bin/phpunit