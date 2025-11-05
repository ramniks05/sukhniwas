# Sukhniwas Guest House Website

A modern, feature-rich PHP website for managing a PG/Guest House with an admin panel for content management.

## Features

### Public Website
- **Home Page**: Hero section with featured rooms
- **Rooms & Rates**: Display all available rooms with pricing
- **Room Details**: Detailed view with image gallery
- **Gallery**: Image gallery for all rooms
- **About**: Information about the guest house
- **Contact/Enquiry Form**: Lead capture form with WhatsApp integration
- **WhatsApp Integration**: Click-to-chat WhatsApp links
- **Responsive Design**: Modern, mobile-friendly UI/UX

### Admin Panel
- **Dashboard**: Statistics and recent enquiries overview
- **Room Management**: Add, edit, delete rooms with full CRUD
- **Image Management**: Upload multiple images per room with thumbnails
- **Enquiry Management**: View, filter, and manage customer enquiries
- **Export Functionality**: Export enquiries to CSV
- **Settings**: Update site information, contact details, SEO settings
- **Secure Authentication**: Password-protected admin area

## Requirements

- PHP 8.0+ (recommended 8.1+)
- MySQL 5.7+ / MariaDB 10.2+
- Apache web server with mod_rewrite
- PHP Extensions:
  - PDO MySQL
  - GD (or ImageMagick)
  - OpenSSL
  - JSON
  - mbstring

## Installation

### 1. Upload Files

Upload all files to your web server (e.g., via FTP or cPanel File Manager).

```
sukhniwas/
├── admin/
├── assets/
├── config/
├── database/
├── includes/
├── public/
└── .htaccess
```

### 2. Database Setup

1. Create a MySQL database:
   ```sql
   CREATE DATABASE sukhniwas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Import the schema:
   - Access phpMyAdmin or MySQL command line
   - Import `database/schema.sql` file
   - This will create all tables and insert default data

### 3. Configuration

1. Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'sukhniwas_db');
   define('DB_USER', 'your_db_username');
   define('DB_PASS', 'your_db_password');
   ```

2. Edit `config/config.php`:
   ```php
   define('SITE_URL', 'https://yourdomain.com/sukhniwas');
   ```

3. **IMPORTANT**: Change the default admin password:
   - Log in with: `admin` / `admin123`
   - Go to Settings and change your password
   - Or update directly in database:
     ```sql
     UPDATE admins SET password_hash = '$2y$10$...' WHERE username = 'admin';
     ```
     (Generate new hash using `password_hash('your_password', PASSWORD_DEFAULT)`)

### 4. File Permissions

Set proper permissions for upload directories:

```bash
chmod 755 public/uploads
chmod 755 public/uploads/rooms
chmod 755 public/uploads/thumbnails
```

Or via cPanel File Manager:
- Right-click on `public/uploads` → Change Permissions → 755
- Repeat for subdirectories

### 5. Create Upload Directories

Create these directories if they don't exist:
- `public/uploads/rooms/`
- `public/uploads/thumbnails/`

### 6. Test the Installation

1. Visit: `https://yourdomain.com/sukhniwas/public/index.php`
2. Admin login: `https://yourdomain.com/sukhniwas/admin/login.php`
   - Username: `admin`
   - Password: `admin123` (CHANGE THIS!)

## Directory Structure

```
sukhniwas/
├── admin/                 # Admin panel files
│   ├── includes/          # Admin header/footer
│   ├── index.php          # Dashboard
│   ├── rooms.php          # Room list
│   ├── room-edit.php      # Add/Edit room
│   ├── room-images.php    # Manage room images
│   ├── enquiries.php      # Enquiry list
│   ├── enquiry-details.php # View enquiry
│   ├── settings.php       # Site settings
│   ├── login.php          # Admin login
│   └── logout.php         # Logout
├── assets/
│   ├── css/
│   │   ├── style.css      # Public site styles
│   │   └── admin.css      # Admin panel styles
│   └── js/
│       ├── main.js        # Public site JS
│       └── admin.js       # Admin panel JS
├── config/
│   ├── config.php         # Main configuration
│   └── database.php       # Database connection
├── database/
│   └── schema.sql         # Database schema
├── includes/
│   ├── functions.php      # Helper functions
│   ├── header.php         # Public header
│   └── footer.php         # Public footer
├── public/
│   ├── index.php          # Home page
│   ├── rooms.php          # Rooms listing
│   ├── room-details.php   # Room details
│   ├── gallery.php        # Gallery
│   ├── about.php          # About page
│   ├── contact.php        # Contact/Enquiry form
│   └── uploads/           # Uploaded files
│       ├── rooms/         # Room images
│       └── thumbnails/    # Thumbnails
└── .htaccess              # Security & routing
```

## Security Notes

1. **Change Default Password**: Immediately change the default admin password
2. **Database Credentials**: Never commit database credentials to version control
3. **File Permissions**: Set upload directories to 755, not 777
4. **HTTPS**: Use SSL certificate for production
5. **Error Reporting**: Disable `display_errors` in production (`config/config.php`)
6. **Admin URL**: Consider renaming `admin/` directory for additional security

## Deployment Checklist (GoDaddy/Shared Hosting)

- [ ] Upload all files via FTP/cPanel
- [ ] Create MySQL database
- [ ] Import `database/schema.sql`
- [ ] Update `config/database.php` with database credentials
- [ ] Update `config/config.php` with your site URL
- [ ] Create upload directories (`public/uploads/rooms`, `public/uploads/thumbnails`)
- [ ] Set file permissions (755 for directories)
- [ ] Test public website
- [ ] Test admin login
- [ ] Change default admin password
- [ ] Update site settings in admin panel
- [ ] Add rooms and images
- [ ] Test enquiry form
- [ ] Test WhatsApp links
- [ ] Disable error display in production
- [ ] Enable HTTPS/SSL

## SEO Features

- Meta titles and descriptions
- Open Graph tags
- Structured data ready
- Semantic HTML
- Mobile-friendly design
- Fast loading times

## Future Enhancements (Optional)

- Online payment integration
- Booking calendar
- Email notifications
- SMS notifications
- Multi-language support
- Customer reviews
- Blog/news section
- Analytics integration
- Social media feed
- Advanced search filters

## Support

For issues or questions:
1. Check database connection settings
2. Verify file permissions
3. Check PHP error logs
4. Ensure all required PHP extensions are enabled

## License

This project is open source and available for use.

## Credits

Built with PHP, MySQL, and modern web technologies.

---

**Note**: Remember to change the default admin password immediately after installation!

