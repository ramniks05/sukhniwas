# Fix Admin Login Issue

If you're unable to login with `admin` / `admin123`, use one of these solutions:

## Solution 1: Reset Password via SQL (Quick Fix)

1. Open phpMyAdmin
2. Select your `sukhniwas` database
3. Go to SQL tab
4. Copy and paste this SQL:

```sql
UPDATE admins 
SET password_hash = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy' 
WHERE username = 'admin';
```

5. Click "Go"
6. Try logging in again with:
   - Username: `admin`
   - Password: `admin123`

## Solution 2: Use Reset Password Tool

1. Visit: `http://localhost/sukhniwas/admin/reset-password.php`
2. Enter a new password (twice)
3. Click "Reset Password"
4. Login with the new password
5. **IMPORTANT:** Delete `admin/reset-password.php` file after use!

## Solution 3: Check Database

Run this SQL to check if admin exists:

```sql
SELECT * FROM admins WHERE username = 'admin';
```

If no results, create the admin:

```sql
INSERT INTO admins (username, password_hash, email, full_name, is_active) 
VALUES ('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'admin@sukhniwas.com', 'Administrator', 1);
```

## Solution 4: Generate New Hash

If you want a different password, generate a new hash:

1. Create a PHP file `hash.php` in project root:
```php
<?php
echo password_hash('your_new_password', PASSWORD_DEFAULT);
```

2. Run it: `http://localhost/sukhniwas/hash.php`
3. Copy the hash
4. Update database:
```sql
UPDATE admins SET password_hash = 'YOUR_NEW_HASH_HERE' WHERE username = 'admin';
```

## Troubleshooting

**Still can't login?**
- Check if `admins` table exists
- Check if admin user exists: `SELECT * FROM admins;`
- Check if `is_active = 1`
- Clear browser cookies
- Check PHP error logs

**Verify the hash works:**
```php
<?php
// Test if hash matches password
$hash = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy';
var_dump(password_verify('admin123', $hash)); // Should return true
```

## After Fixing

1. Login successfully
2. Go to Settings (if available)
3. Change your password to something secure
4. Delete `reset-password.php` if you used it

---

**Default Credentials (after fix):**
- Username: `admin`
- Password: `admin123`

