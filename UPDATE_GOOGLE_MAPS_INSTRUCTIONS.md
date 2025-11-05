# How to Update Google Maps Embed Code

## Quick Method (Using the Provided Link)

I've created a SQL file (`database/update_google_maps.sql`) that will automatically embed the map for the location from your Google Maps link.

**Location:** Faridabad, Haryana  
**Coordinates:** 28.345386, 77.302082  
**Google Maps Link:** https://maps.app.goo.gl/42zTbFpbuuHiirTQ8

### Option 1: Run SQL File (Quickest)
1. Open phpMyAdmin
2. Select the `sukhniwas` database
3. Go to the "SQL" tab
4. Open `database/update_google_maps.sql`
5. Copy and paste the SQL into the phpMyAdmin SQL box
6. Click "Go"

### Option 2: Via Admin Panel
1. Login to Admin Panel: `http://localhost/sukhniwas/admin/`
2. Go to **Settings**
3. Scroll to **Google Maps** section
4. Get the embed code (see instructions below)
5. Paste it in the "Google Map Embed Code" field
6. Click **Save Settings**

---

## How to Get the Embed Code from Google Maps

### Method 1: From the Share Link
1. Open the Google Maps link: https://maps.app.goo.gl/42zTbFpbuuHiirTQ8
2. Click the **"Share"** button (on the left sidebar or top)
3. Click **"Embed a map"** tab
4. Select map size (recommended: Medium or Large)
5. Copy the entire `<iframe>` code
6. Paste it in the Admin Panel Settings → Google Maps Embed Code field

### Method 2: Direct Embed URL
If you want a simpler embed without using the Share dialog, you can use:
```html
<iframe src="https://www.google.com/maps?q=28.345386,77.302082&hl=en&z=17&output=embed" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
```

---

## Where the Map Will Appear

Once you update the Google Maps embed code, the map will automatically appear on:
- **Contact Page** (`public/contact.php`) - Below the contact information
- The map will be responsive and adjust to different screen sizes

---

## Customizing the Map

### Change Zoom Level
In the embed URL, change the `z=17` parameter:
- `z=15` - Zoomed out (shows wider area)
- `z=17` - Medium zoom (recommended)
- `z=19` - Zoomed in (shows street detail)

### Change Map Size
In the iframe code, modify the `height` attribute:
- `height="300"` - Small
- `height="450"` - Medium (recommended)
- `height="600"` - Large

---

## Troubleshooting

### Map Not Showing
- Make sure you copied the complete `<iframe>` tag (including opening and closing tags)
- Check that the `src` attribute in the iframe is a valid Google Maps URL
- Clear browser cache and refresh the page

### Map Too Small/Large
- Adjust the `height` attribute in the iframe code
- The width is set to `100%` so it will adjust to container size automatically

---

## Address to Update

While updating the map, also update the **Address** field in Settings with the full address:
- Location: Faridabad, Haryana
- Plus Code: 88W2+5R4

You can add the complete street address from the PDF brochure in the **Address** field in Admin Settings.
