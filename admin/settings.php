<?php
require_once __DIR__ . '/../config/config.php';
requireAdminLogin();

$db = getDB();
$success = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => sanitize($_POST['site_name'] ?? ''),
        'site_email' => sanitize($_POST['site_email'] ?? ''),
        'site_phone' => sanitize($_POST['site_phone'] ?? ''),
        'site_phone_2' => sanitize($_POST['site_phone_2'] ?? ''),
        'whatsapp_number' => sanitize($_POST['whatsapp_number'] ?? ''),
        'whatsapp_message' => sanitize($_POST['whatsapp_message'] ?? ''),
        'address' => sanitize($_POST['address'] ?? ''),
        'facebook_url' => sanitize($_POST['facebook_url'] ?? ''),
        'instagram_url' => sanitize($_POST['instagram_url'] ?? ''),
        'google_map_embed' => $_POST['google_map_embed'] ?? '',
        'seo_title' => sanitize($_POST['seo_title'] ?? ''),
        'seo_description' => sanitize($_POST['seo_description'] ?? ''),
        'seo_keywords' => sanitize($_POST['seo_keywords'] ?? ''),
        'food_available' => sanitize($_POST['food_available'] ?? ''),
        'food_type' => sanitize($_POST['food_type'] ?? ''),
        'food_description' => sanitize($_POST['food_description'] ?? ''),
        'food_timings' => sanitize($_POST['food_timings'] ?? ''),
        'food_pricing' => sanitize($_POST['food_pricing'] ?? ''),
        'additional_services' => sanitize($_POST['additional_services'] ?? ''),
    ];
    
    try {
        foreach ($settings as $key => $value) {
            updateSiteSetting($key, $value);
        }
        $success = true;
    } catch (Exception $e) {
        $error = 'Failed to save settings: ' . $e->getMessage();
    }
}

// Get current settings
$currentSettings = [];
$stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $stmt->fetch()) {
    $currentSettings[$row['setting_key']] = $row['setting_value'];
}

$pageTitle = 'Settings';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <div class="page-header-admin">
        <h1>Site Settings</h1>
        <p>Update website information and preferences</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">Settings saved successfully!</div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" class="admin-form">
        <div class="form-section">
            <h3>Basic Information</h3>
            
            <div class="form-group">
                <label for="site_name">Site Name</label>
                <input type="text" id="site_name" name="site_name" 
                       value="<?php echo htmlspecialchars($currentSettings['site_name'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($currentSettings['address'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3>Contact Information</h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="site_email">Email</label>
                    <input type="email" id="site_email" name="site_email" 
                           value="<?php echo htmlspecialchars($currentSettings['site_email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="site_phone">Phone 1</label>
                    <input type="text" id="site_phone" name="site_phone" 
                           value="<?php echo htmlspecialchars($currentSettings['site_phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="site_phone_2">Phone 2 (Optional)</label>
                    <input type="text" id="site_phone_2" name="site_phone_2" 
                           value="<?php echo htmlspecialchars($currentSettings['site_phone_2'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="whatsapp_number">WhatsApp Number</label>
                    <input type="text" id="whatsapp_number" name="whatsapp_number" 
                           placeholder="919876543210 (with country code, no +)"
                           value="<?php echo htmlspecialchars($currentSettings['whatsapp_number'] ?? ''); ?>">
                    <small>Format: 919876543210 (no + or spaces)</small>
                </div>
                
                <div class="form-group">
                    <label for="whatsapp_message">WhatsApp Message</label>
                    <input type="text" id="whatsapp_message" name="whatsapp_message" 
                           placeholder="Default message for WhatsApp link"
                           value="<?php echo htmlspecialchars($currentSettings['whatsapp_message'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Social Media</h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="facebook_url">Facebook URL</label>
                    <input type="url" id="facebook_url" name="facebook_url" 
                           value="<?php echo htmlspecialchars($currentSettings['facebook_url'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="instagram_url">Instagram URL</label>
                    <input type="url" id="instagram_url" name="instagram_url" 
                           value="<?php echo htmlspecialchars($currentSettings['instagram_url'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Google Maps</h3>
            
            <div class="form-group">
                <label for="google_map_embed">Google Map Embed Code</label>
                <textarea id="google_map_embed" name="google_map_embed" rows="4" 
                          placeholder="Paste the iframe embed code from Google Maps"><?php echo htmlspecialchars($currentSettings['google_map_embed'] ?? ''); ?></textarea>
                <small>Get embed code from Google Maps → Share → Embed a map</small>
            </div>
        </div>

        <div class="form-section">
            <h3>Food/Mess Services</h3>
            
            <div class="form-group">
                <label for="food_available">Food Service Available?</label>
                <select id="food_available" name="food_available">
                    <option value="">Select...</option>
                    <option value="yes" <?php echo ($currentSettings['food_available'] ?? '') === 'yes' ? 'selected' : ''; ?>>Yes</option>
                    <option value="no" <?php echo ($currentSettings['food_available'] ?? '') === 'no' ? 'selected' : ''; ?>>No</option>
                </select>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="food_type">Food Type</label>
                    <select id="food_type" name="food_type">
                        <option value="">Select...</option>
                        <option value="vegetarian" <?php echo ($currentSettings['food_type'] ?? '') === 'vegetarian' ? 'selected' : ''; ?>>Vegetarian</option>
                        <option value="non-vegetarian" <?php echo ($currentSettings['food_type'] ?? '') === 'non-vegetarian' ? 'selected' : ''; ?>>Non-Vegetarian</option>
                        <option value="both" <?php echo ($currentSettings['food_type'] ?? '') === 'both' ? 'selected' : ''; ?>>Both (Veg & Non-Veg)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="food_timings">Food Timings</label>
                    <input type="text" id="food_timings" name="food_timings" 
                           placeholder="e.g., Breakfast: 8-10 AM, Lunch: 12-2 PM, Dinner: 7-9 PM"
                           value="<?php echo htmlspecialchars($currentSettings['food_timings'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="food_pricing">Food Pricing</label>
                <input type="text" id="food_pricing" name="food_pricing" 
                       placeholder="e.g., Breakfast: ₹50, Lunch: ₹80, Dinner: ₹80, Monthly: ₹3000"
                       value="<?php echo htmlspecialchars($currentSettings['food_pricing'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="food_description">Food Description</label>
                <textarea id="food_description" name="food_description" rows="4" 
                          placeholder="Describe your food services, menu, quality, etc."><?php echo htmlspecialchars($currentSettings['food_description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="additional_services">Additional Services/Facilities</label>
                <textarea id="additional_services" name="additional_services" rows="3" 
                          placeholder="e.g., Laundry, Wi-Fi, Power Backup, Parking, etc."><?php echo htmlspecialchars($currentSettings['additional_services'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3>SEO Settings</h3>
            
            <div class="form-group">
                <label for="seo_title">SEO Title</label>
                <input type="text" id="seo_title" name="seo_title" 
                       value="<?php echo htmlspecialchars($currentSettings['seo_title'] ?? ''); ?>">
                <small>Appears in browser tab and search results</small>
            </div>
            
            <div class="form-group">
                <label for="seo_description">SEO Description</label>
                <textarea id="seo_description" name="seo_description" rows="3"><?php echo htmlspecialchars($currentSettings['seo_description'] ?? ''); ?></textarea>
                <small>Meta description for search engines (150-160 characters recommended)</small>
            </div>
            
            <div class="form-group">
                <label for="seo_keywords">SEO Keywords</label>
                <input type="text" id="seo_keywords" name="seo_keywords" 
                       placeholder="guest house, accommodation, rooms"
                       value="<?php echo htmlspecialchars($currentSettings['seo_keywords'] ?? ''); ?>">
                <small>Comma-separated keywords</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

