# How to Remove Old Rooms and Prepare for Client's PDF Data

## Quick Method: Admin Panel (Recommended)

1. **Login to Admin Panel**
   - URL: `http://localhost/sukhniwas/admin/login.php`
   - Username: `admin`
   - Password: `admin123`

2. **Navigate to Rooms Page**
   - Click **Rooms** in the admin menu
   - Or go to: `http://localhost/sukhniwas/admin/rooms.php`

3. **Clear All Rooms**
   - Click the red **🗑️ Clear All** button (top right)
   - Review the list of rooms that will be deleted
   - Type **"DELETE ALL"** in the confirmation box
   - Click **Delete All Rooms**
   - ✅ Done! All old sample rooms and images are removed

4. **Add New Rooms**
   - Click **➕ Add First New Room** button
   - Or go to **Rooms** → **Add New Room**
   - Enter room details from client's PDF
   - Upload images
   - Repeat for all rooms

---

## Alternative Method: SQL Script

1. **Open phpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`
   - Select `sukhniwas` database

2. **Run SQL Script**
   - Click **SQL** tab
   - Open file: `database/remove_old_rooms.sql`
   - Copy and paste the SQL commands
   - Click **Go**

3. **Verify**
   - Check that `rooms` table is empty
   - Check that `room_images` table is empty

---

## What Gets Deleted?

✅ **Deleted:**
- All rooms (Deluxe, Standard, Premium sample rooms)
- All room images (automatically via CASCADE)

⚠️ **Preserved:**
- Admin users
- Site settings
- Enquiries (but room_id becomes NULL)

---

## After Clearing

1. Review client's PDF for room details
2. Add new rooms via Admin Panel
3. Or share room details with me to create a custom SQL file
4. Upload images for each room

---

## Need Help?

If you need help adding rooms based on the PDF:
- Share room names, prices, descriptions, amenities
- I'll create a custom SQL insert file
- Or guide you through Admin Panel setup
