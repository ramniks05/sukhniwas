# Gallery Setup Instructions

## Current Setup

The gallery currently has **hardcoded images and videos** for demonstration purposes. These use sample images from Unsplash and YouTube videos.

## Future Setup - Using Local Images

### Step 1: Create Image Directory Structure

Create the following directories:
```
assets/
└── images/
    ├── gallery/
    │   ├── images/
    │   └── videos/
```

### Step 2: Add Your Images

1. Place your **images** in: `assets/images/gallery/images/`
   - Supported formats: JPG, PNG, WebP
   - Recommended size: 800x600px or larger
   - Naming convention: `room-1.jpg`, `room-2.jpg`, etc.

2. Place your **video files** in: `assets/images/gallery/videos/`
   - Supported formats: MP4, WebM, OGG, MOV
   - Recommended: MP4 format
   - Naming convention: `room-tour-1.mp4`, `facilities-tour.mp4`, etc.

### Step 3: Update Gallery Configuration

Edit `public/gallery.php` and replace the hardcoded array with code that reads from the `assets/images` folder:

```php
// Function to scan and load images/videos from assets/images
function loadGalleryFromAssets() {
    $galleryPath = __DIR__ . '/../assets/images/gallery/';
    $mediaItems = [];
    
    // Load images
    $imagesPath = $galleryPath . 'images/';
    if (is_dir($imagesPath)) {
        $images = glob($imagesPath . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);
        foreach ($images as $image) {
            $mediaItems[] = [
                'type' => 'image',
                'url' => str_replace(__DIR__ . '/../', SITE_URL . '/', $image),
                'title' => basename($image, '.' . pathinfo($image, PATHINFO_EXTENSION)),
                'caption' => basename($image, '.' . pathinfo($image, PATHINFO_EXTENSION))
            ];
        }
    }
    
    // Load videos
    $videosPath = $galleryPath . 'videos/';
    if (is_dir($videosPath)) {
        $videos = glob($videosPath . '*.{mp4,webm,ogg,mov}', GLOB_BRACE);
        foreach ($videos as $video) {
            $mediaItems[] = [
                'type' => 'video',
                'url' => str_replace(__DIR__ . '/../', SITE_URL . '/', $video),
                'title' => basename($video, '.' . pathinfo($video, PATHINFO_EXTENSION)),
                'caption' => basename($video, '.' . pathinfo($video, PATHINFO_EXTENSION)),
                'thumbnail' => '' // Generate thumbnail or use placeholder
            ];
        }
    }
    
    return $mediaItems;
}

// Use this instead of hardcoded array
$hardcodedMedia = loadGalleryFromAssets();
```

### Step 4: Alternative - Use Database

You can also add images/videos through the Admin Panel:
1. Go to Admin Panel → Rooms
2. Select a room → Manage Images
3. Upload images or add video URLs
4. Images will automatically appear in the gallery

## Current Hardcoded Items

The gallery currently shows:
- **10 Images** (sample room photos from Unsplash)
- **2 Videos** (sample YouTube videos - replace with your own)

## To Replace Hardcoded Videos

Edit `public/gallery.php` and replace the YouTube URLs:

```php
[
    'type' => 'video',
    'url' => 'YOUR_YOUTUBE_VIDEO_URL', // Replace this
    'title' => 'Your Video Title',
    'caption' => 'Your video description',
    'thumbnail' => 'YOUR_THUMBNAIL_URL' // Optional
],
```

## Image Optimization Tips

1. **Image Size**: Keep images under 1MB for faster loading
2. **Resolution**: 1920x1080px is good for web (they'll be resized automatically)
3. **Format**: Use JPEG for photos, PNG for graphics with transparency
4. **Naming**: Use descriptive names like `deluxe-room-interior.jpg`

## Video Optimization Tips

1. **Format**: MP4 (H.264) is most compatible
2. **Resolution**: 1080p (1920x1080) is recommended
3. **File Size**: Keep under 50MB if hosting locally
4. **Alternative**: Use YouTube/Vimeo URLs instead of local files (saves server space)

## Next Steps

1. ✅ Current: Hardcoded sample images/videos are working
2. ⏭️ Future: Replace with local images from `assets/images/gallery/`
3. ⏭️ Future: Or use Admin Panel to manage gallery items

## Notes

- The gallery will combine hardcoded items with database items
- Database items (added via Admin Panel) will appear after hardcoded items
- You can remove hardcoded items once you have your own images/videos
