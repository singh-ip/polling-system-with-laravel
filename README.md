# Polling System

Real-time polling application built with Laravel and WebSockets.

## Quick Start

### Prerequisites
- Docker & Docker Compose

### Setup

```bash
# Clone and build
git clone <repo>
cd polling-system-with-laravel
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Create admin user
docker-compose exec app php artisan admin:create "Admin" "admin@example.com" "password"

# Run tests
docker-compose exec app php artisan test
```

### Creating Polls

```bash
docker-compose exec app php artisan poll:create "What's your favorite color?" "Red" "Blue" "Green"
```

### Accessing the System

- **Public polls**: http://localhost:8000/polls/{poll-id}
- **Admin panel**: http://localhost:8000/login
- **Logout**: POST to `/logout` (button on admin page)

## Features

- Create polls with multiple options (CLI)
- Vote once per user/IP
- Real-time vote count updates via WebSockets
- Admin panel to manage polls
- Secure login for admin users

## Tech Stack

- Laravel 12 (PHP 8.4)
- SQLite (testing), MySQL (production)
- WebSockets via Soketi
- Docker & Docker Compose

## Testing

All code follows TDD with unit and feature tests.

```bash
docker-compose exec app php artisan test
```

## Project Structure

```
app/
  Console/Commands/      - CLI commands (poll:create, admin:create)
  Events/                - VoteCast event (for broadcasting)
  Http/
    Controllers/         - PollController, AuthController
    Middleware/          - EnsureUserIsAdmin
    Requests/            - VoteRequest, LoginRequest
  Models/                - Poll, PollOption, Vote, User
  Services/              - PollService (business logic)
resources/
  views/
    auth/                - login.blade.php
    poll/                - show.blade.php (public), index.blade.php (admin)
tests/
  Feature/               - Integration tests
  Unit/                  - Unit tests
```

## Routes

```
GET  /                          - Welcome page
GET  /login                     - Login form
POST /login                     - Submit login
POST /logout                    - Logout (requires auth)
GET  /polls/{poll}              - View poll (public)
POST /polls/{poll}/vote         - Submit vote (rate limited)
GET  /polls                     - Admin listing (requires auth + admin role)
```
- ✅ **One-Vote Enforcement**: Prevents duplicate votes per user/IP
- ✅ **CSRF Protection**: Built-in Laravel form protection

---

### 1. Get Started

To get started, follow the **Setup & Run** section below.

```bash
git config --global --add safe.directory /var/www/html/polling-system-with-laravel
cd /var/www/html/polling-system-with-laravel
```

### 2. Build & Start Services

```bash
# Build app image (PHP 8.4) and start all services
docker-compose build --no-cache app
docker-compose up -d

# Install composer dependencies inside the app container
docker-compose run --rm app composer install --ignore-platform-reqs
```

Verify services are running:
```bash
docker-compose ps
```

### 3. Database Setup

Run migrations to create database tables:

```bash
docker-compose exec app php artisan migrate --force
```

### 4. Create Admin User (Required for Admin Panel)

An admin user is required to access the admin polls listing via the UI.

#### Step 4a: Create Admin via CLI

```bash
docker-compose exec app php artisan admin:create "Admin Name" "admin@example.com" "password123"
```

Example:
```bash
docker-compose exec app php artisan admin:create "John Doe" "john@polls.local" "secure_password"
```

**Output**: "Admin user 'John Doe' created successfully!"

#### Step 4b: Login to Admin Panel

1. Visit: `http://127.0.0.1:8000/login`
2. Enter admin credentials (email and password from step 4a)
3. Click "Login"
4. You'll be redirected to `/polls` (Admin Polls Listing)

**Features on Admin Panel:**
- View all polls with vote counts
- Pagination (10 polls per page)
- Direct links to each poll
- Logout button (top right)
- User info display

#### Security

✅ **Admin-Only Access**: Only users with `is_admin=true` can login
✅ **Protected Routes**: `/polls` (admin listing) requires authentication and admin role
✅ **Rate Limiting**: Vote endpoint limited to 10 requests/minute to prevent abuse
✅ **Automatic Logout**: Non-admin users are rejected at login

### 5. Create a Poll (We can add security for admin to handle it as an extended feature)

```bash
docker-compose exec app php artisan poll:create "What's your favorite color?" "Red" "Blue" "Green"
```

### 6. Access the Poll

- **Public poll page**: Visit `http://127.0.0.1:8000/polls/{id}` (replace `{id}` with the poll ID)
- **Admin listing**: Visit `http://127.0.0.1:8000/polls` (requires admin login)

### 7. Verify Real-time Broadcasting

Open the poll page in **two separate browser windows** (or one normal + one incognito):
1. Both should connect to Soketi (you'll see WebSocket connection in DevTools → Network)
2. Vote from one window
3. Watch the vote counts update instantly in both windows (no page refresh needed)

## How It Works

```
Admin creates poll via CLI or UI
  ↓
Poll stored in database
  ↓
User opens /polls/{id}
  ↓
Browser connects to Soketi WebSocket server
  ↓
User votes
  ↓
Server validates vote (one per user/IP)
  ↓
Server broadcasts VoteCast event
  ↓
All browsers receive update → vote counts refresh in real-time
```

## Development

### Run Tests

```bash
docker-compose exec app php artisan test

# Or specific test
docker-compose exec app ./vendor/bin/phpunit --filter VoteTest
```

### Clear Config Cache

After changing `.env`:
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Environment Variables

The `.env` file includes:

```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=soketi 
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_USE_TLS=false
```

**Note**: The app (running in Docker) connects to `soketi:6001` via Docker DNS. The browser (running on host) connects to `127.0.0.1:6001` (hardcoded in Blade view).

## Troubleshooting

### No WebSocket connection in browser

- Check that Soketi is running: `docker-compose ps soketi`
- Verify port 6001 is accessible: `curl -i http://127.0.0.1:6001`
- Check browser DevTools → Network → WS for connection errors
- View Soketi logs: `docker-compose logs soketi`

### Votes not broadcasting

- Ensure app can reach Soketi: `docker-compose exec app php -r "echo gethostbyname('soketi');"`
- Check Laravel logs: `docker-compose logs app | tail -50`
- Confirm `BROADCAST_DRIVER=pusher` in `.env`

### "You have already voted" error

This is expected behavior — each user/IP can vote once per poll. To vote again, use incognito/different browser or clear cookies.