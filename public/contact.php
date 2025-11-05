<?php
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Contact Us - ' . getSiteSetting('site_name');
$pageDescription = 'Get in touch with us for bookings and enquiries.';

$success = false;
$error = '';
$selectedRoom = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $mobile = sanitize($_POST['mobile'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $check_in = sanitize($_POST['check_in'] ?? '');
    $check_out = sanitize($_POST['check_out'] ?? '');
    $room_id = intval($_POST['room_id'] ?? 0);
    $message = sanitize($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($mobile)) {
        $error = 'Name and mobile number are required.';
    } elseif (!preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $mobile))) {
        $error = 'Please enter a valid 10-digit mobile number.';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO enquiries (name, mobile, email, check_in_date, check_out_date, room_id, message, ip_address, user_agent) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $check_in_date = !empty($check_in) ? date('Y-m-d', strtotime($check_in)) : null;
            $check_out_date = !empty($check_out) ? date('Y-m-d', strtotime($check_out)) : null;
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $stmt->execute([$name, $mobile, $email ?: null, $check_in_date, $check_out_date, $room_id ?: null, $message, $ip_address, $user_agent]);
            
            // Send email notification
            sendEnquiryEmail([
                'name' => $name,
                'mobile' => $mobile,
                'email' => $email,
                'check_in_date' => $check_in_date,
                'check_out_date' => $check_out_date,
                'room_id' => $room_id,
                'message' => $message
            ]);
            
            $success = true;
            
            // Clear form data
            $_POST = [];
        } catch (Exception $e) {
            $error = 'Something went wrong. Please try again later.';
        }
    }
}

// Get selected room if provided
if (isset($_GET['room'])) {
    $room_id = intval($_GET['room']);
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM rooms WHERE id = ? AND is_visible = 1");
    $stmt->execute([$room_id]);
    $selectedRoom = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main">
    <div class="page-header">
        <div class="container">
            <h1>Contact Us</h1>
            <p>Get in touch for bookings and enquiries</p>
        </div>
    </div>

    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h2>Send Enquiry</h2>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <strong>Thank you!</strong> Your enquiry has been submitted successfully. We'll contact you soon.
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="contact-form" id="enquiryForm">
                        <div class="form-group">
                            <label for="name">Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="mobile">Mobile Number <span class="required">*</span></label>
                            <input type="tel" id="mobile" name="mobile" required 
                                   pattern="[0-9]{10}" 
                                   placeholder="10-digit mobile number"
                                   value="<?php echo htmlspecialchars($_POST['mobile'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email (Optional)</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="check_in">Check-in Date (Optional)</label>
                                <input type="date" id="check_in" name="check_in" 
                                       min="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="check_out">Check-out Date (Optional)</label>
                                <input type="date" id="check_out" name="check_out" 
                                       min="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <?php if ($selectedRoom): ?>
                            <div class="form-group">
                                <label>Selected Room</label>
                                <div class="selected-room-info">
                                    <strong><?php echo htmlspecialchars($selectedRoom['title']); ?></strong>
                                    <span><?php 
                                        $price = $selectedRoom['price_per_night'] ?? 0;
                                        $pricingType = $selectedRoom['pricing_type'] ?? 'per_night';
                                        if (($selectedRoom['room_type'] ?? '') === 'pg' && $pricingType === 'per_night') {
                                            $pricingType = 'per_month';
                                        }
                                        echo formatPrice($price, $pricingType);
                                    ?></span>
                                </div>
                                <input type="hidden" name="room_id" value="<?php echo $selectedRoom['id']; ?>">
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <label for="room_id">Room (Optional)</label>
                                <select id="room_id" name="room_id">
                                    <option value="">Select a room</option>
                                    <?php
                                    $db = getDB();
                                    $stmt = $db->query("SELECT id, title FROM rooms WHERE is_visible = 1 ORDER BY title");
                                    while ($room = $stmt->fetch()):
                                    ?>
                                        <option value="<?php echo $room['id']; ?>" 
                                                <?php echo (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($room['title']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="message">Message (Optional)</label>
                            <textarea id="message" name="message" rows="5" 
                                      placeholder="Any special requirements or questions..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-large">Submit Enquiry</button>
                    </form>
                </div>
                
                <!-- Contact Info -->
                <div class="contact-info-wrapper">
                    <h2>Get in Touch</h2>
                    
                    <div class="contact-info-card">
                        <div class="contact-item">
                            <div class="contact-icon">📞</div>
                            <div>
                                <h4>Phone</h4>
                                <p><a href="tel:<?php echo getSiteSetting('site_phone'); ?>"><?php echo getSiteSetting('site_phone'); ?></a></p>
                                <?php if ($phone2 = getSiteSetting('site_phone_2')): ?>
                                    <p><a href="tel:<?php echo $phone2; ?>"><?php echo $phone2; ?></a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">📧</div>
                            <div>
                                <h4>Email</h4>
                                <p><a href="mailto:<?php echo getSiteSetting('site_email'); ?>"><?php echo getSiteSetting('site_email'); ?></a></p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">📍</div>
                            <div>
                                <h4>Address</h4>
                                <p><?php echo getSiteSetting('address', ''); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="whatsapp-cta">
                        <a href="<?php echo getWhatsAppLink(getSiteSetting('whatsapp_number')); ?>" 
                           target="_blank" 
                           class="btn btn-whatsapp btn-large btn-block">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.386 1.262.617 1.694.789.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.372a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Chat on WhatsApp
                        </a>
                    </div>
                    
                    <?php if ($map = getSiteSetting('google_map_embed')): ?>
                        <div class="map-container">
                            <?php echo $map; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Date validation
document.getElementById('check_in')?.addEventListener('change', function() {
    const checkOut = document.getElementById('check_out');
    if (checkOut && this.value) {
        checkOut.min = this.value;
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

