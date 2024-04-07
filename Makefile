.PHONY: *

.DEFAULT_GOAL := test

test:
	@docker run -it --rm --name fp-db-test -v "$$PWD":/app -w /app php:8.3-cli-alpine php test.php