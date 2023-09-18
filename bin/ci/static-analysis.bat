@echo Performing static analysis...
@docker exec --tty php ^
    ./vendor/bin/phpstan analyse ^
    -c /app/phpstan.neon --memory-limit=-1 --no-progress