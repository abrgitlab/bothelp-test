deploy:
	cp docker-compose.override.yaml.dist docker-compose.override.yaml
	docker-compose up -d
	docker exec -it bothelp-test_php composer install --no-dev
	docker exec -it bothelp-test_php chmod +x ./consume.sh

generate:
	docker exec -it bothelp-test_php bin/console generate-data

consume:
	docker exec -it bothelp-test_php ./consume.sh

stop:
	docker-compose down --remove-orphans
