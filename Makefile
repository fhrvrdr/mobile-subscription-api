.PHONY: build start stop

build:
	cp -f subscription-management-api/.env.example subscription-management-api/.env
	docker compose build
	docker compose up -d
	sleep 10
	docker compose exec subscription-api composer install
	docker compose exec subscription-api php artisan migrate:install
	docker compose exec subscription-api php artisan migrate
	docker compose exec subscription-api php artisan db:seed
	docker compose exec subscription-api php artisan jwt:generate-secret
	docker compose exec subscription-api php artisan key:generate
	docker compose exec subscription-api php artisan scout:index 'App\Models\Device'
	docker compose exec subscription-api php artisan scout:index 'App\Models\Subscription'
	sleep 10
	docker compose stop
start:
	docker compose up -d
stop:
	docker compose stop