# Parade Weather API

REST API for managing weather events and parades, built with Symfony 7.3 using API Platform, MySQL, and JWT authentication.

## 🚀 Tech Stack

- **PHP**: 8.2+
- **Symfony**: 7.3
- **API Platform**: 4.2
- **MySQL**: 8.0
- **JWT Authentication**: Lexik JWT Bundle
- **Docker**: Docker Compose
- **Doctrine ORM**: 3.5

## 📋 System Requirements

- PHP 8.2 or higher
- Composer
- Docker and Docker Compose
- Git

## 🛠 Installation and Setup

### 1. Clone Repository

```bash
git clone git@github.com:Akjol06/Parade-Weather.git
cd Parade_Weather/parade-weather
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Create `.env.local` file in the project root:

```bash
cp .env .env.local
```

Configure the following variables in `.env.local`:

```env
# Database
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/parade_weather?serverVersion=8.0"

# JWT Settings
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_jwt_passphrase_here

# Symfony Settings
APP_ENV=dev
APP_SECRET=your_app_secret_here
```

### 4. Generate JWT Keys

```bash
# Create JWT keys directory
mkdir -p config/jwt

# Generate private key
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096

# Generate public key
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout

# Set proper permissions
chmod 644 config/jwt/public.pem
chmod 600 config/jwt/private.pem
```

### 5. Database Setup

```bash
# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate
```

### 6. Start Symfony Server

```bash
# Development mode
symfony server:start

# Or using built-in PHP server
php -S localhost:8000 -t public/
```

## 🏗 Project Structure

```
parade-weather/
├── config/                 # Configuration files
│   ├── packages/          # Package settings
│   ├── routes/            # Routes
│   └── jwt/               # JWT keys
├── src/
│   ├── Controller/        # API Controllers
│   ├── Entity/           # Doctrine entities
│   ├── Repository/       # Repositories
│   ├── DTO/              # Data Transfer Objects
│   ├── Service/          # Business logic
│   └── Security/         # Authentication
├── migrations/           # Database migrations
├── public/               # Public files
└── templates/            # Templates
```

## 🔧 Useful Commands

### Database
```bash
# Create migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Rollback migration
php bin/console doctrine:migrations:migrate prev

# Clear cache
php bin/console cache:clear
```

### Development
```bash
# Generate controller
php bin/console make:controller

# Generate entity
php bin/console make:entity

# View routes
php bin/console debug:router

# Configuration info
php bin/console debug:config
```

## 🧪 Testing

```bash
# Code checking
vendor/bin/php-cs-fixer fix
```

## 📚 API Documentation

After starting the server, API documentation is available at:

- **Swagger UI**: `http://localhost:8000/api`

## 🐳 Docker Commands

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# View logs
docker compose logs -f

# Restart containers
docker compose restart

# Remove volumes (be careful!)
docker compose down -v
```

## 🔒 Security

- JWT tokens have 7-day expiration
- Passwords are hashed using bcrypt
- CORS configured for cross-domain requests
- API uses stateless authentication

## 🚨 Troubleshooting

### Database Issues
```bash
# Check connection
php bin/console doctrine:database:create --if-not-exists

# Reset database
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### JWT Issues
```bash
# Check key permissions
ls -la config/jwt/

# Regenerate keys
rm config/jwt/private.pem config/jwt/public.pem
# Repeat step 4 from installation section
```

### Cache Issues
```bash
# Clear all caches
php bin/console cache:clear --env=dev
php bin/console cache:clear --env=prod
```

## 📞 Support

If you encounter issues:

1. Check logs: `var/log/dev.log`
2. Ensure all dependencies are installed
3. Check environment variable settings
4. Make sure Docker containers are running

---

**Happy coding! 🚀**