# Deployment Guide for Sukhniwas Guest House Website

## Pre-Deployment Checklist

### 1. Local Testing
- [ ] Test all public pages
- [ ] Test admin panel functionality
- [ ] Test image uploads
- [ ] Test enquiry form submission
- [ ] Test WhatsApp links
- [ ] Verify responsive design on mobile/tablet
- [ ] Check all links work correctly

### 2. Code Preparation
- [ ] Update `config/config.php` with production URL
- [ ] Update `config/database.php` with production database credentials
- [ ] Set `display_errors` to 0 in production
- [ ] Remove any test/development code
- [ ] Verify all file paths use correct constants

### 3. Database Preparation
- [ ] Backup existing database (if any)
- [ ] Export schema.sql ready for import
- [ ] Prepare database credentials
- [ ] Note: Default admin credentials (change after deployment!)

## Deployment Steps for GoDaddy/Shared Hosting

### Step 1: Prepare Files
1. Ensure all files are ready
2. Compress files into a ZIP (optional, for easier upload)
3. Keep `database/schema.sql` ready for import

### Step 2: Upload Files
**Via cPanel File Manager:**
1. Log in to cPanel
2. Navigate to File Manager
3. Go to `public_html` (or your domain's root folder)
4. Create folder `sukhniwas` (or use existing)
5. Upload all files maintaining directory structure

**Via FTP:**
1. Connect using FTP client (FileZilla, etc.)
2. Navigate to `public_html/sukhniwas/`
3. Upload all files maintaining directory structure

### Step 3: Database Setup
1. In cPanel, go to "MySQL Databases"
2. Create a new database: `sukhniwas_db`
3. Create a database user and assign to database
4. Note down: database name, username, password
5. Go to phpMyAdmin
6. Select your database
7. Click "Import"
8. Choose `database/schema.sql`
9. Click "Go" to import

### Step 4: Configure Database Connection
1. Edit `config/database.php` via File Manager or FTP
2. Update these lines:
   ```php
   define('DB_HOST', 'localhost');  // Usually 'localhost' for shared hosting
   define('DB_NAME', 'your_db_name');  // Database name from Step 3
   define('DB_USER', 'your_db_user');  // Database user from Step 3
   define('DB_PASS', 'your_db_pass');  // Database password from Step 3
   ```

### Step 5: Configure Site URL
1. Edit `config/config.php`
2. Update:
   ```php
   define('SITE_URL', 'https://yourdomain.com/sukhniwas');
   ```
3. If using subdomain: `https://sukhniwas.yourdomain.com`
4. If in root: `https://yourdomain.com`

### Step 6: Set File Permissions
**Via cPanel File Manager:**
1. Right-click `public/uploads` folder
2. Select "Change Permissions"
3. Set to `755` (or `0755`)
4. Repeat for:
   - `public/uploads/rooms`
   - `public/uploads/thumbnails`

**Via SSH (if available):**
```bash
chmod 755 public/uploads
chmod 755 public/uploads/rooms
chmod 755 public/uploads/thumbnails
```

### Step 7: Create Upload Directories
If directories don't exist:
1. In File Manager, navigate to `public/uploads/`
2. Create folder `rooms`
3. Create folder `thumbnails`
4. Set permissions to 755 for both

### Step 8: Disable Error Display (Production)
1. Edit `config/config.php`
2. Change:
   ```php
   error_reporting(0);  // or E_ALL & ~E_DEPRECATED & ~E_STRICT
   ini_set('display_errors', 0);
   ```

### Step 9: Test Installation
1. **Public Site:**
   - Visit: `https://yourdomain.com/sukhniwas/public/index.php`
   - Check all pages load correctly
   - Test enquiry form

2. **Admin Panel:**
   - Visit: `https://yourdomain.com/sukhniwas/admin/login.php`
   - Login: `admin` / `admin123`
   - **IMMEDIATELY CHANGE PASSWORD!**

### Step 10: Post-Deployment Tasks
1. **Change Admin Password:**
   - Log in to admin panel
   - Go to Settings (or update directly in database)
   - Change default password

2. **Update Site Settings:**
   - Go to Admin → Settings
   - Update contact information
   - Update WhatsApp number
   - Update address
   - Update SEO settings

3. **Add Content:**
   - Add rooms via Admin → Rooms
   - Upload room images
   - Test enquiry form

4. **Test Everything:**
   - Submit test enquiry
   - Check WhatsApp links
   - Verify email notifications (if configured)
   - Test image uploads

## SSL/HTTPS Setup (Important!)

1. **Enable SSL in cPanel:**
   - Go to "SSL/TLS Status"
   - Enable SSL for your domain
   - Use free Let's Encrypt certificate

2. **Force HTTPS:**
   Add to `.htaccess` in root:
   ```apache
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

3. **Update SITE_URL in config.php:**
   - Change `http://` to `https://`

## Troubleshooting

### Issue: "Database connection failed"
- Check database credentials in `config/database.php`
- Verify database exists in cPanel
- Check database user has proper permissions
- Try using `127.0.0.1` instead of `localhost` (some hosts)

### Issue: "Permission denied" for uploads
- Check folder permissions (should be 755)
- Verify folder ownership
- Check if `.htaccess` in uploads allows file access

### Issue: Images not displaying
- Check upload directory permissions
- Verify `ROOM_IMAGES_URL` in config matches actual path
- Check file paths in database

### Issue: "404 Not Found" errors
- Verify `.htaccess` is uploaded
- Check mod_rewrite is enabled (contact hosting support)
- Verify file paths are correct

### Issue: Admin login not working
- Clear browser cookies
- Check session directory is writable
- Verify database has admin user
- Try resetting password in database

## Security Recommendations

1. **Rename Admin Directory:**
   - Rename `admin/` to something unique (e.g., `admin_xyz123/`)
   - Update links in admin header/footer

2. **Use Strong Passwords:**
   - Change default admin password
   - Use complex passwords

3. **Regular Backups:**
   - Backup database regularly
   - Backup uploads directory
   - Use cPanel backup tool or manual exports

4. **Keep Updated:**
   - Update PHP version if possible
   - Keep server software updated
   - Monitor for security issues

5. **Additional Security:**
   - Consider IP whitelisting for admin area
   - Use two-factor authentication (future enhancement)
   - Regular security audits

## Performance Optimization

1. **Enable Caching:**
   - Use browser caching (already in `.htaccess`)
   - Consider PHP opcode caching (OPcache)

2. **Optimize Images:**
   - Compress images before upload
   - Use WebP format when possible
   - Thumbnails are auto-generated

3. **Database Optimization:**
   - Regular database cleanup
   - Remove old enquiries periodically
   - Optimize database tables

## Maintenance

### Regular Tasks:
- [ ] Backup database weekly
- [ ] Check enquiry submissions
- [ ] Update room availability
- [ ] Monitor disk space
- [ ] Review error logs
- [ ] Update content/images

### Monthly Tasks:
- [ ] Review and clean old enquiries
- [ ] Optimize database
- [ ] Check for security updates
- [ ] Review site analytics

## Support Resources

- **GoDaddy Support:** https://www.godaddy.com/help
- **cPanel Documentation:** https://docs.cpanel.net
- **PHP Documentation:** https://www.php.net/docs.php

---

**Remember:** Always backup before making changes!

