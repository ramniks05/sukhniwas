# Quick Start Guide

## 5-Minute Setup

### 1. Database Setup
```sql
-- Run this in phpMyAdmin or MySQL:
-- Import database/schema.sql
```

### 2. Configuration
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sukhniwas_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

Edit `config/config.php`:
```php
define('SITE_URL', 'http://localhost/sukhniwas');
```

### 3. File Permissions
```bash
chmod 755 public/uploads/rooms
chmod 755 public/uploads/thumbnails
```

### 4. Access
- **Website:** `http://localhost/sukhniwas/public/index.php`
- **Admin:** `http://localhost/sukhniwas/admin/login.php`
  - Username: `admin`
  - Password: `admin123` (CHANGE THIS!)

### 5. First Steps
1. Log in to admin panel
2. Go to Settings → Update contact info
3. Add your first room
4. Upload room images
5. **Change admin password!**

## Default Credentials

**Admin Login:**
- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT:** Change this password immediately after first login!

## Troubleshooting

**"Database connection failed"**
- Check credentials in `config/database.php`
- Verify database exists and user has permissions

**"Permission denied" for uploads**
- Set folder permissions to 755
- Check folder exists: `public/uploads/rooms/`

**Images not showing**
- Check `ROOM_IMAGES_URL` in config matches actual path
- Verify upload directory permissions

## Need Help?

See `README.md` for detailed documentation.
See `DEPLOYMENT.md` for production deployment guide.

