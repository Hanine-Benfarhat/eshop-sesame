# 🚀 Quick Start Guide for Collaborators

Welcome to the Sesame Marketplace project! This guide will help you get up and running quickly.

## ⚡ Quick Setup (5 minutes)

### Step 1: Clone & Install
```bash
# Clone the repository
git clone https://github.com/your-username/eshop-sesame.git
cd eshop-sesame

# Install dependencies
composer install
```

### Step 2: Database Setup
```bash
# Create database
symfony console doctrine:database:create

# Sync migrations (important!)
symfony console doctrine:migrations:sync-metadata-storage

# Apply existing migrations
symfony console doctrine:migrations:migrate --no-interaction

# Add the phone column if not exists
symfony console doctrine:query:sql "ALTER TABLE user ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL"
```

### Step 3: Start Development Server
```bash
symfony server:start
```

Visit: **http://127.0.0.1:8000**

## 🔑 Test Accounts

| Email | Password | Role |
|-------|----------|------|
| admin@sesame.com | admin123 | Admin |
| seller@sesame.com | seller123 | Seller |
| user@sesame.com | user123 | User |

## 📝 Common Development Tasks

### Working with Migrations

```bash
# If you get "metadata storage not up to date" error
symfony console doctrine:migrations:sync-metadata-storage

# If migration fails with "table already exists"
symfony console doctrine:query:sql "INSERT IGNORE INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\\\\VersionXXXXXXXXXXXXXX', NOW(), 1)"

# Generate new migration after entity changes
symfony console make:migration

# Apply migrations
symfony console doctrine:migrations:migrate
```

### Entity Changes Workflow

```bash
# 1. Modify your entity in src/Entity/
# 2. Generate migration
symfony console make:migration

# 3. Review the migration file in migrations/
# 4. Apply the migration
symfony console doctrine:migrations:migrate

# 5. Clear cache
symfony console cache:clear
```

### Clear Cache Issues

```bash
# Clear all cache
symfony console cache:clear

# Warm up cache
symfony console cache:warmup

# If permission issues on Windows
icacls var /grant Everyone:F /t
```

## 🌿 Git Workflow

### Creating a Feature Branch

```bash
# Update main branch
git checkout main
git pull origin main

# Create feature branch
git checkout -b feature/your-feature-name

# Make your changes...
git add .
git commit -m "Add: description of changes"

# Push to your branch
git push origin feature/your-feature-name
```

### Before Creating Pull Request

```bash
# 1. Update from main
git checkout main
git pull origin main

# 2. Merge main into your feature branch
git checkout feature/your-feature-name
git merge main

# 3. Resolve conflicts if any
# 4. Test your changes
symfony php bin/phpunit

# 5. Push and create PR
git push origin feature/your-feature-name
```

## 🐛 Common Issues & Solutions

### Issue: Server won't start
```bash
# Check if port is in use
symfony server:stop
symfony server:start --port=8001
```

### Issue: Database connection failed
1. Check MySQL is running
2. Verify credentials in `.env.local`
3. Ensure database exists: `symfony console doctrine:database:create`

### Issue: 500 Error after pulling changes
```bash
# Clear cache
symfony console cache:clear

# Update database
symfony console doctrine:migrations:migrate

# Check logs
tail -f var/log/dev.log
```

### Issue: Permission denied errors
```bash
# Windows
icacls var /grant Everyone:F /t

# Linux/Mac
chmod -R 777 var/
```

## 📂 Important Files to Know

| File/Folder | Purpose |
|-------------|---------|
| `src/Entity/` | Database models |
| `src/Controller/` | Application logic |
| `templates/` | Twig templates |
| `config/packages/` | Bundle configs |
| `migrations/` | Database migrations |
| `.env` | Environment config (don't edit!) |
| `.env.local` | Local overrides (create this) |

## 🎨 Making Changes

### Adding a New Feature

1. **Create Entity** (if needed)
   ```bash
   symfony console make:entity
   ```

2. **Create Migration**
   ```bash
   symfony console make:migration
   symfony console doctrine:migrations:migrate
   ```

3. **Create Controller**
   ```bash
   symfony console make:controller YourController
   ```

4. **Create Template**
   - Add to `templates/` directory
   - Extend `base.html.twig` or `admin/base.html.twig`

5. **Test Your Changes**
   - Browse to your route
   - Check for errors in console

### Modifying Existing Code

1. **Find the file** using the structure:
   - Controllers → `src/Controller/`
   - Templates → `templates/`
   - Entities → `src/Entity/`

2. **Make changes**

3. **Clear cache**
   ```bash
   symfony console cache:clear
   ```

4. **Test** your changes

## 📊 Checking Database

```bash
# View all tables
symfony console doctrine:query:sql "SHOW TABLES"

# View table structure
symfony console doctrine:query:sql "DESCRIBE user"

# View data
symfony console doctrine:query:sql "SELECT * FROM user LIMIT 5"
```

## 🔍 Debugging Tips

### Check Logs
```bash
# View real-time logs
tail -f var/log/dev.log

# View last 50 lines
tail -50 var/log/dev.log
```

### Symfony Profiler
When you see an error, look for the toolbar at the bottom of the page with debug info.

### Database Issues
```bash
# Check migration status
symfony console doctrine:migrations:status

# See SQL queries
symfony console doctrine:schema:update --dump-sql
```

## 🤝 Pull Request Checklist

Before submitting your PR:

- [ ] Code follows PSR-12 standards
- [ ] No syntax errors
- [ ] Cache cleared and tested
- [ ] Migrations work correctly
- [ ] Meaningful commit messages
- [ ] Updated documentation if needed
- [ ] Branch is up to date with main

## 💬 Getting Help

- Check the main [README.md](README.md) for detailed docs
- Review existing code for examples
- Ask team members in Slack/Discord
- Check Symfony documentation: https://symfony.com/doc

## 🎯 Current Sprint Goals

*(Update this section with current development priorities)*

- [ ] Implement messaging system between buyers/sellers
- [ ] Add product reviews and ratings
- [ ] Enhance search functionality
- [ ] Add product categories management
- [ ] Implement email notifications

---

**Happy Coding! 🎉**

*Last updated: February 20, 2026*