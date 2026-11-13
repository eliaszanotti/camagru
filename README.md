# Camagru

Camagru is a photo sharing application. Users can create accounts, publish photos, browse the public gallery, and like or comment on posts.

## Requirements

- Docker and Docker Compose
- PHP CLI (to run the test suite)
- Node.js and npm (only needed to rebuild the CSS)

## Configure and start the application

From the repository root, create the local environment file:

```sh
cp php/.sample.env php/.env
```

Set `POSTGRES_PASSWORD` in `php/.env`. Set the same password in the `DATABASE_URI` value. SMTP settings are needed to send email; they can be configured in the same file.

Build and start the app and database:

```sh
docker compose -f php/docker-compose.yml up --build -d
```

Open [http://localhost:8080](http://localhost:8080).

## Useful commands

Run these from the repository root.

### Start and stop

```sh
# Start services in the background
docker compose -f php/docker-compose.yml up -d

# Stop services and keep the database data
docker compose -f php/docker-compose.yml down

# Follow PHP and Apache logs
docker compose -f php/docker-compose.yml logs -f php
```

### Run the test suite

Start the app and database first, then run:

```sh
php php/tests/run.php
```

The page tests use `http://localhost:8080` by default. To use a different URL, set `CAMAGRU_BASE_URL` when starting the runner:

```sh
CAMAGRU_BASE_URL=http://localhost:8081 php php/tests/run.php
```

### Seed the database

With the app and database running, execute:

```sh
docker compose -f php/docker-compose.yml exec php php database/seed.php
```

The seed creates or updates ten verified accounts (`test1@test.fr` through `test10@test.fr`), 100 published sample posts, and 0–5 random comments per post. It generates sample SVG images in `php/uploads/posts/`, using the same storage directory and `image_path` format as regular posts. Running it again replaces the seed posts and comments while preserving unrelated posts.

All test accounts use this password:

```text
MotDePasse1!
```

### Rebuild CSS

```sh
cd php
npm install
npm run build
```
