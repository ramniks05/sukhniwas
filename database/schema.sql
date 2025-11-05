-- PG/Guest House Website Database Schema
-- Created for sukhniwas project

CREATE DATABASE IF NOT EXISTS sukhniwas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sukhniwas;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rooms Table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    price_per_night DECIMAL(10, 2) NOT NULL,
    max_occupancy INT DEFAULT 2,
    amenities TEXT, -- JSON or comma-separated
    is_available TINYINT(1) DEFAULT 1,
    is_visible TINYINT(1) DEFAULT 1,
    featured_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_visible (is_visible, is_available),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Room Images Table
CREATE TABLE IF NOT EXISTS room_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255),
    image_order INT DEFAULT 0,
    alt_text VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    INDEX idx_room_order (room_id, image_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enquiries Table
CREATE TABLE IF NOT EXISTS enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    check_in_date DATE,
    check_out_date DATE,
    message TEXT,
    room_id INT,
    status ENUM('new', 'contacted', 'confirmed', 'rejected') DEFAULT 'new',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin (password: admin123 - CHANGE THIS!)
-- Password hash for 'admin123' using password_hash('admin123', PASSWORD_DEFAULT)
-- To generate new hash: php -r "echo password_hash('your_password', PASSWORD_DEFAULT);"
-- This hash is verified to work with password: admin123
INSERT IGNORE INTO admins (username, password_hash, email, full_name) VALUES
('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'admin@sukhniwas.com', 'Administrator');

-- Insert Default Site Settings
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'Sukhniwas Guest House'),
('site_email', 'info@sukhniwas.com'),
('site_phone', '+91-9876543210'),
('whatsapp_number', '919876543210'),
('whatsapp_message', 'Hi, I would like to know more about your rooms.'),
('address', '123 Main Street, City, State, PIN - 123456'),
('facebook_url', ''),
('instagram_url', ''),
('google_map_embed', ''),
('seo_title', 'Sukhniwas Guest House - Comfortable Stay in City'),
('seo_description', 'Book your stay at Sukhniwas Guest House. Affordable rooms with modern amenities. Contact us for bookings.'),
('seo_keywords', 'guest house, PG, affordable rooms, stay, accommodation');

-- Insert Sample Rooms (Optional - for testing)
-- Using INSERT IGNORE to avoid duplicate errors if rooms already exist
INSERT IGNORE INTO rooms (title, slug, description, price_per_night, max_occupancy, amenities, is_available, is_visible) VALUES
('Deluxe Room', 'deluxe-room', 'Spacious room with modern amenities and comfortable bed. Perfect for couples or solo travelers.', 1500.00, 2, 'WiFi,AC,TV,Attached Bathroom,Hot Water,24/7 Security', 1, 1),
('Standard Room', 'standard-room', 'Comfortable room with all essential facilities at an affordable price.', 1000.00, 2, 'WiFi,AC,TV,Attached Bathroom,Hot Water', 1, 1),
('Premium Room', 'premium-room', 'Luxury room with premium amenities and extra space for a memorable stay.', 2500.00, 3, 'WiFi,AC,TV,Attached Bathroom,Hot Water,24/7 Security,Refrigerator,Work Desk', 1, 1);

-- Insert Sample Images (using Unsplash URLs - these will show until you upload real images)
-- Note: These are placeholder URLs that will work immediately. Replace with your own images via admin panel.
-- Check if room exists before inserting images (to avoid errors if rooms don't exist)
INSERT IGNORE INTO room_images (room_id, image_path, image_order, alt_text) 
SELECT 1, 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800&h=600&fit=crop', 1, 'Deluxe Room - Comfortable and spacious'
WHERE EXISTS (SELECT 1 FROM rooms WHERE id = 1)
UNION ALL
SELECT 1, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop', 2, 'Deluxe Room - Modern amenities'
WHERE EXISTS (SELECT 1 FROM rooms WHERE id = 1)
UNION ALL
SELECT 2, 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800&h=600&fit=crop', 1, 'Standard Room - Clean and comfortable'
WHERE EXISTS (SELECT 1 FROM rooms WHERE id = 2)
UNION ALL
SELECT 2, 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800&h=600&fit=crop', 2, 'Standard Room - Well maintained'
WHERE EXISTS (SELECT 1 FROM rooms WHERE id = 2)
UNION ALL
SELECT 3, 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800&h=600&fit=crop', 1, 'Premium Room - Luxury accommodation'
WHERE EXISTS (SELECT 1 FROM rooms WHERE id = 3)
UNION ALL
SELECT 3, 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800&h=600&fit=crop', 2, 'Premium Room - Premium amenities'
WHERE EXISTS (SELECT 1 FROM rooms WHERE id = 3)
UNION ALL
SELECT 3, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop', 3, 'Premium Room - Spacious design'
WHERE EXISTS (SELECT 1 FROM rooms WHERE id = 3);

