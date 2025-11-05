# How to Access the Website

## Correct URLs for XAMPP

### Public Website:
1. **Homepage:**
   - `http://localhost/sukhniwas/` (auto-redirects to public)
   - `http://localhost/sukhniwas/public/index.php`

2. **Other Pages:**
   - Rooms: `http://localhost/sukhniwas/public/rooms.php`
   - Gallery: `http://localhost/sukhniwas/public/gallery.php`
   - About: `http://localhost/sukhniwas/public/about.php`
   - Contact: `http://localhost/sukhniwas/public/contact.php`

### Admin Panel:
- Login: `http://localhost/sukhniwas/admin/login.php`
- Dashboard: `http://localhost/sukhniwas/admin/index.php`

## If You Get "Forbidden" Error:

1. **Make sure you're accessing a PHP file, not a directory:**
   - ✅ Correct: `http://localhost/sukhniwas/public/index.php`
   - ❌ Wrong: `http://localhost/sukhniwas/public/` (directory)

2. **Check Apache is running:**
   - Open XAMPP Control Panel
   - Make sure Apache is "Running" (green)

3. **Check file permissions:**
   - Files should be readable
   - No special permissions needed on Windows

4. **Try accessing directly:**
   - Use full path: `http://localhost/sukhniwas/public/index.php`

## Troubleshooting:

- **"Forbidden" error:** Access a specific PHP file, not a directory
- **"Page not found":** Check Apache is running
- **"Database connection failed":** Import the database schema first
- **White page:** Check PHP errors in Apache error log

