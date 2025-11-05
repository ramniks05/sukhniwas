# Video Support Added to Gallery

## What's New

The gallery now supports both **images** and **videos**! You can:
- Upload image files (as before)
- Add video URLs (YouTube, Vimeo, or direct video links)
- Upload video files directly (MP4, WebM, OGG, MOV)

## Setup Instructions

### Step 1: Run Database Migration

1. Open phpMyAdmin
2. Select the `sukhniwas` database
3. Go to the **SQL** tab
4. Open `database/add_video_support.sql`
5. Copy and paste the SQL into phpMyAdmin
6. Click **Go**

This will add:
- `media_type` column (image/video)
- `video_url` column (for YouTube/Vimeo URLs)
- Indexes for better performance

### Step 2: Verify Migration

After running the SQL, you should see:
- Two new columns in the `room_images` table
- All existing images marked as 'image' type

## How to Add Videos

### Method 1: Video URL (Recommended for YouTube/Vimeo)

1. Login to Admin Panel
2. Go to **Rooms** → Select a room → Click **Manage Images**
3. Click the **🎥 Video URL** tab
4. Paste your video URL:
   - **YouTube**: `https://www.youtube.com/watch?v=...` or `https://youtu.be/...`
   - **Vimeo**: `https://vimeo.com/...`
   - **Direct video URL**: Any direct link to MP4/WebM video
5. Click **Add Video**

**Benefits:**
- No file storage needed
- Automatic thumbnail for YouTube videos
- Fast loading
- No server bandwidth usage

### Method 2: Upload Video File

1. Login to Admin Panel
2. Go to **Rooms** → Select a room → Click **Manage Images**
3. Click the **📁 Upload Video** tab
4. Select a video file (MP4, WebM, OGG, MOV)
5. Max file size: **50MB**
6. Click **Upload Video**

**Supported Formats:**
- MP4 (recommended)
- WebM
- OGG
- MOV/QuickTime

## Video Features

### In Gallery Page
- Videos show with a play icon overlay
- Video badge indicator
- YouTube videos get automatic thumbnail
- Click to play in lightbox

### In Lightbox
- **YouTube/Vimeo**: Plays in embedded player
- **Direct URLs**: Plays in HTML5 video player
- **Uploaded videos**: Plays in HTML5 video player
- Close button stops video playback
- Press **Escape** to close

### Video Display
- Videos appear in the gallery grid alongside images
- Video thumbnails are clearly marked
- Play icon indicates it's a video
- Responsive design works on all devices

## Admin Panel Features

### Manage Media
- View all images and videos in one place
- See media type indicator (Image/Video)
- Reorder media (videos and images together)
- Delete videos same as images

### Tabs Interface
The upload form has three tabs:
1. **📷 Image** - Upload images (as before)
2. **🎥 Video URL** - Add YouTube/Vimeo/direct URLs
3. **📁 Upload Video** - Upload video files

## Technical Details

### Database Schema
```sql
ALTER TABLE room_images 
ADD COLUMN media_type ENUM('image', 'video') DEFAULT 'image';

ALTER TABLE room_images 
ADD COLUMN video_url VARCHAR(500) NULL;
```

### File Limits
- **Images**: 5MB max
- **Videos**: 50MB max
- **Supported video formats**: MP4, WebM, OGG, MOV, AVI

### Video Storage
- Uploaded videos stored in: `public/uploads/rooms/`
- Filename format: `video_vid_[timestamp]_[timestamp].mp4`
- URLs stored in `video_url` column (for YouTube/Vimeo)

## Tips

1. **For YouTube videos**: Just paste the URL, thumbnail is automatic
2. **For large videos**: Use YouTube/Vimeo instead of direct upload (saves server space)
3. **Video quality**: Upload high-quality videos for best results
4. **File size**: Compress large videos before uploading (keep under 50MB)

## Troubleshooting

### Video not playing
- Check if video format is supported (MP4 recommended)
- Verify file size is under 50MB
- Check browser supports HTML5 video

### YouTube thumbnail not showing
- Ensure URL is correct YouTube format
- Video must be public or unlisted
- Try refreshing the page

### Upload fails
- Check file size (must be under 50MB)
- Verify file format is supported
- Check server upload limits in php.ini

## Next Steps

1. Run the database migration (`database/add_video_support.sql`)
2. Test by adding a YouTube video URL to a room
3. Upload a test video file if needed
4. Check the gallery page to see videos displayed

Enjoy your new video gallery feature! 🎥✨
