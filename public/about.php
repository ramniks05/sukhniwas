<?php
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'About Us - ' . getSiteSetting('site_name');
$pageDescription = 'Learn more about ' . getSiteSetting('site_name') . ' and our commitment to providing comfortable accommodation.';

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main">
    <div class="page-header">
        <div class="container">
            <h1>About Us</h1>
            <p>Your trusted accommodation partner</p>
        </div>
    </div>

    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Welcome to <?php echo getSiteSetting('site_name', 'Sukhniwas Guest House'); ?></h2>
                    <p>
                        We are committed to providing you with a comfortable and memorable stay. 
                        Our guest house offers clean, well-maintained rooms with modern amenities 
                        at affordable prices.
                    </p>
                    <p>
                        Whether you're traveling for business or leisure, we ensure a pleasant 
                        experience with our friendly staff, secure premises, and convenient location.
                    </p>
                    
                    <h3>Why Choose Us?</h3>
                    <ul class="features-list">
                        <li>✓ Clean and comfortable rooms</li>
                        <li>✓ Affordable pricing</li>
                        <li>✓ 24/7 security</li>
                        <li>✓ Free WiFi</li>
                        <li>✓ Convenient location</li>
                        <li>✓ Friendly and helpful staff</li>
                    </ul>
                </div>
                
                <div class="about-info">
                    <div class="info-card">
                        <h4>Our Location</h4>
                        <p><?php echo getSiteSetting('address', '123 Main Street, City, State'); ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h4>Contact Us</h4>
                        <p>📞 <a href="tel:<?php echo getSiteSetting('site_phone'); ?>"><?php echo getSiteSetting('site_phone'); ?></a></p>
                        <?php if ($phone2 = getSiteSetting('site_phone_2')): ?>
                            <p>📞 <a href="tel:<?php echo $phone2; ?>"><?php echo $phone2; ?></a></p>
                        <?php endif; ?>
                        <p>📧 <a href="mailto:<?php echo getSiteSetting('site_email'); ?>"><?php echo getSiteSetting('site_email'); ?></a></p>
                    </div>
                    
                    <div class="info-card">
                        <h4>Quick Contact</h4>
                        <a href="<?php echo getWhatsAppLink(getSiteSetting('whatsapp_number')); ?>" target="_blank" class="btn btn-whatsapp">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.386 1.262.617 1.694.789.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.372a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Chat on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($map = getSiteSetting('google_map_embed')): ?>
        <div class="container" style="margin-top: 3rem;">
            <div class="map-container">
                <h3 style="margin-bottom: 1.5rem; text-align: center;">📍 Find Us on Map</h3>
                <?php echo $map; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

