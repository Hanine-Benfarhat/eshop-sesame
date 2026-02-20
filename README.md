# 🛒 Sesame Marketplace

A modern e-commerce marketplace built with Symfony 7.4 where users can buy and sell products with an admin approval system.

![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?style=flat&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)

## 📋 Table of Contents

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [User Roles](#user-roles)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

## ✨ Features

### For Buyers
- 🔍 Browse approved products
- 📧 Contact sellers directly (email/phone)
- 🔐 Secure user authentication
- 📱 Responsive design

### For Sellers
- ➕ List products for sale
- 📝 Manage product listings
- ⏳ Track approval status
- 📊 View product analytics

### For Admins
- ✅ Approve/reject product listings
- 👥 Manage users and roles
- 📈 View analytics and reports
- 🎛️ Full system control

## 🔧 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP >= 8.2** with extensions:
  - `ext-ctype`
  - `ext-iconv`
  - `pdo_mysql`
  - `json`
  - `mbstring`
- **Composer** (latest version)
- **MySQL 8.0+** or MariaDB 10.11+
- **Symfony CLI** (recommended)
- **Node.js & NPM** (for asset management)

### Check Your PHP Version

```bash
php -v
```

### Install Symfony CLI (Windows)

```powershell
# Using Scoop
scoop install symfony-cli

# Or download from https://symfony.com/download
```

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/eshop-sesame.git
cd eshop-sesame
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Configure Environment Variables

Copy the `.env` file and update your database credentials:

```bash
# Copy the example environment file
copy .env .env.local  # Windows
# or
cp .env .env.local    # Linux/Mac
```

Edit `.env.local` and configure your database:

```env
# Database Configuration
DATABASE_URL="mysql://root:@127.0.0.1:3306/projetSesame?serverVersion=8.0.32&charset=utf8mb4"

# App Secret (generate with: php bin/console secrets:generate-keys)
APP_SECRET=your-secret-key-here

# JWT Configuration
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-passphrase-here
```

### 4. Generate JWT Keys

```bash
# Create JWT directory
mkdir config/jwt

# Generate keys
php bin/console lexik:jwt:generate-keypair
```

## 🗄️ Database Setup

### 1. Create the Database

```bash
# Using Symfony Console
symfony console doctrine:database:create

# Or using PHP directly
php bin/console doctrine:database:create
```

### 2. Run Migrations

```bash
# Check migration status
symfony console doctrine:migrations:status

# Sync metadata storage (if needed)
symfony console doctrine:migrations:sync-metadata-storage

# Run migrations
symfony console doctrine:migrations:migrate --no-interaction
```

### 3. Load Sample Data (Optional)

```bash
# Load fixtures for testing
symfony console doctrine:fixtures:load --no-interaction
```

## 🚀 Running the Application

### Start the Development Server

```bash
# Using Symfony CLI (recommended)
symfony server:start

# Or using PHP built-in server
php -S localhost:8000 -t public/
```

The application will be available at: **http://127.0.0.1:8000**

### Access Different Areas

- **Homepage**: http://127.0.0.1:8000
- **Browse Products**: http://127.0.0.1:8000/product
- **Register**: http://127.0.0.1:8000/register
- **Login**: http://127.0.0.1:8000/login
- **Admin Panel**: http://127.0.0.1:8000/admin/dashboard (requires admin role)
- **User Dashboard**: http://127.0.0.1:8000/dashboard (requires login)

## 👤 User Roles

### Default Test Accounts

After running fixtures, you can use these accounts:

| Email | Password | Role | Description |
|-------|----------|------|-------------|
| admin@sesame.com | admin123 | Admin | Full access to admin panel |
| seller@sesame.com | seller123 | Seller | Can create and manage products |
| user@sesame.com | user123 | Buyer | Can browse and contact sellers |

### Role Capabilities

#### ROLE_USER (Buyer)
- Browse approved products
- Contact sellers
- View product details

#### ROLE_SELLER
- All ROLE_USER capabilities
- Create product listings
- Edit own products
- Track approval status

#### ROLE_ADMIN
- All ROLE_SELLER capabilities
- Approve/reject products
- Manage all users
- Access analytics dashboard
- Modify user roles

## 🔌 API Endpoints

### Authentication

```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "roles": ["ROLE_USER"]
  }
}
```

### Products

```http
# Get all products
GET /api/products

# Get single product
GET /api/products/{id}

# Create product (requires authentication)
POST /api/products
Authorization: Bearer {token}
```

## 🧪 Testing

### Run PHPUnit Tests

```bash
# Run all tests
symfony php bin/phpunit

# Run specific test
symfony php bin/phpunit tests/Controller/ProductControllerTest.php

# Run with coverage
symfony php bin/phpunit --coverage-html var/coverage
```

## 🛠️ Development Commands

### Clear Cache

```bash
symfony console cache:clear
```

### Create a New Migration

```bash
# After modifying entities
symfony console make:migration

# Review the migration file in migrations/ directory

# Apply the migration
symfony console doctrine:migrations:migrate
```

### Create New Fixtures

```bash
symfony console make:fixtures
```

### Check Code Quality

```bash
# PHP CS Fixer (if installed)
vendor/bin/php-cs-fixer fix src/

# PHPStan (if installed)
vendor/bin/phpstan analyse src/
```

## 🐛 Troubleshooting

### Common Issues

#### Issue: "The metadata storage is not up to date"

```bash
symfony console doctrine:migrations:sync-metadata-storage
```

#### Issue: "Table already exists" during migration

```bash
# Mark existing migrations as executed
symfony console doctrine:query:sql "INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\\\\Version20260112223859', NOW(), 1)"
```

#### Issue: Server won't start (port already in use)

```bash
# Stop existing server
symfony server:stop

# Start on different port
symfony server:start --port=8001
```

#### Issue: Permission denied on var/cache or var/log

```bash
# Windows
icacls var /grant Everyone:F /t

# Linux/Mac
chmod -R 777 var/
```

### Database Issues

#### Reset Database (⚠️ WARNING: Deletes all data)

```bash
# Drop database
symfony console doctrine:database:drop --force

# Recreate database
symfony console doctrine:database:create

# Run migrations
symfony console doctrine:migrations:migrate --no-interaction

# Load fixtures
symfony console doctrine:fixtures:load --no-interaction
```

## 📁 Project Structure

```
eshop-sesame/
├── assets/              # Frontend assets (JS, CSS)
├── bin/                 # Executables (console, phpunit)
├── config/              # Configuration files
│   ├── packages/        # Bundle configurations
│   └── routes/          # Routing configurations
├── migrations/          # Database migrations
├── public/              # Public web root
│   ├── index.php        # Front controller
│   └── uploads/         # Uploaded files
├── src/
│   ├── Controller/      # Application controllers
│   ├── Entity/          # Doctrine entities
│   ├── Form/            # Form types
│   └── Repository/      # Database repositories
├── templates/           # Twig templates
│   ├── admin/          # Admin panel templates
│   ├── product/        # Product templates
│   ├── security/       # Auth templates
│   └── user/           # User dashboard templates
├── tests/              # PHPUnit tests
└── var/                # Cache and logs
```

## 🤝 Contributing

### Setting Up for Development

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make your changes**
4. **Run tests**
   ```bash
   symfony php bin/phpunit
   ```

5. **Commit your changes**
   ```bash
   git add .
   git commit -m "Add: your feature description"
   ```

6. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request**

### Coding Standards

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed

### Git Workflow

```bash
# Keep your fork up to date
git remote add upstream https://github.com/original/eshop-sesame.git
git fetch upstream
git merge upstream/main

# Create feature branch
git checkout -b feature/amazing-feature

# Make changes and commit
git commit -m "Add: amazing feature"

# Push to your fork
git push origin feature/amazing-feature
```

## 📝 Environment Variables Reference

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_ENV` | Application environment | `dev`, `prod`, `test` |
| `APP_SECRET` | Application secret key | Random string |
| `DATABASE_URL` | Database connection string | `mysql://user:pass@host:port/db` |
| `JWT_SECRET_KEY` | JWT private key path | `%kernel.project_dir%/config/jwt/private.pem` |
| `JWT_PUBLIC_KEY` | JWT public key path | `%kernel.project_dir%/config/jwt/public.pem` |
| `JWT_PASSPHRASE` | JWT key passphrase | Random string |
| `MAILER_DSN` | Email server configuration | `smtp://localhost` |

## 🔒 Security

### Important Security Notes

- **Never commit** `.env.local` or `config/jwt/` to version control
- **Change default passwords** in production
- **Use HTTPS** in production environments
- **Keep dependencies updated**: `composer update`

### Reporting Security Issues

Please report security vulnerabilities to: security@sesame-marketplace.com

## 📜 License

This project is proprietary software. All rights reserved.

## 👨‍💻 Authors

- **Your Name** - *Initial work* - [YourGitHub](https://github.com/yourusername)

## 🙏 Acknowledgments

- Symfony Framework team
- All contributors who helped improve this project
- Bootstrap Icons for the icon set

## 📞 Support

For support, email support@sesame-marketplace.com or create an issue on GitHub.

---

**Built with ❤️ using Symfony 7.4**

Last updated: February 20, 2026