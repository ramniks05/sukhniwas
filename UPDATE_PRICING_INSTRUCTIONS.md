# How to Update Room Pricing from Client's PDF

## Remove Old Rooms First

Before updating, you may want to remove old sample rooms:

**Method 1: Via Admin Panel (Easiest)**
1. Login to Admin Panel: `http://localhost/sukhniwas/admin/`
2. Go to **Rooms** → Click **🗑️ Clear All** button
3. Type "DELETE ALL" to confirm
4. All old rooms and images will be deleted

**Method 2: Via SQL**
- Run `database/remove_old_rooms.sql` in phpMyAdmin

---

## Quick Update - ₹1200 per night

I've created a SQL file to update all rooms to **₹1200 per night** as mentioned.

### Method 1: Using phpMyAdmin (Recommended)
1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Select the `sukhniwas` database from the left sidebar
3. Click on the **SQL** tab
4. Open the file `database/update_room_pricing.sql` in a text editor
5. Copy all the SQL commands
6. Paste into the SQL text area in phpMyAdmin
7. Click **Go** to execute
8. You should see all rooms updated to ₹1200

### Method 2: Using Command Line
```bash
mysql -u root sukhniwas < database/update_room_pricing.sql
```

### Method 3: Using Admin Panel
1. Login to Admin Panel: `http://localhost/sukhniwas/admin/`
2. Go to **Rooms** → Click **Edit** on each room
3. Update **Price per Night** to `1200`
4. Click **Save**

---

## If PDF Has Different Room Types/Prices

If the PDF shows multiple room types with different prices (e.g., Single PG, Double PG, Guest House rooms), please:

1. **Option A**: Share the room details from the PDF and I'll create a custom SQL file
   - Room names
   - Prices for each
   - Amenities
   - Descriptions

2. **Option B**: Use the template in `ROOM_DATA_TEMPLATE.md`
   - Fill in the details
   - Share it with me
   - I'll generate the exact SQL for you

3. **Option C**: Update manually via Admin Panel
   - Login → Rooms → Add/Edit rooms
   - Enter details directly

---

## Current Status
- ✅ SQL update file created: `database/update_room_pricing.sql`
- ✅ Template created: `ROOM_DATA_TEMPLATE.md`
- ⏳ Waiting for confirmation or additional room details from PDF

---

## Need Help?
If the PDF has specific room names, descriptions, or different pricing for different room types, please share those details and I'll update the database accordingly!
