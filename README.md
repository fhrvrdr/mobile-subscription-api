# Subscription Management API

## Requirements
- Docker
- Docker Compose
- Make
- Postman

## Installation
Clone the project repository:

```bash
git clone https://github.com/fhrvrdr/subscription-api.git
```

The first time you run the project, you need to build the Docker image:

```bash
make build
```

Start the containers:

```bash
make start
```

## Technical Details

- PHP version: 8.4
- The project is built using the Laravel framework.
- The database is a MySQL container.
- The search functionality is implemented using the Laravel Scout package and the Meilisearch search engine.
- The cache is handled by a Redis container.
- The API is served by an Nginx container.
- The mock project is built using the Go / Fiber framework.

## Notes

- The Postman collection is available in the `Backend Assessment.postman_collection.json` file.
- Please add a new environment in Postman and set the following variables:
    - `subscription-url` to `http://localhost/api`
    - `mock-url` to `http://localhost:3000`
- If you want to change queue and schedule workers, you can do so in the `subscription-management-api/docker/8.4/supervisord.conf` file.