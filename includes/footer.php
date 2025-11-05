    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo getSiteSetting('site_name', 'Sukhniwas Guest House'); ?></h3>
                    <p><?php echo getSiteSetting('address', ''); ?></p>
                    <?php if ($map = getSiteSetting('google_map_embed')): ?>
                        <div class="footer-map">
                            <?php 
                            // Extract and modify iframe for smaller footer map
                            $mapIframe = $map;
                            // Replace height in iframe if present
                            $mapIframe = preg_replace('/height="[^"]*"/', 'height="150"', $mapIframe);
                            // If no height attribute, add it
                            if (strpos($mapIframe, 'height=') === false) {
                                $mapIframe = str_replace('<iframe', '<iframe height="150"', $mapIframe);
                            }
                            echo $mapIframe; 
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="social-links">
                        <?php if ($fb = getSiteSetting('facebook_url')): ?>
                            <a href="<?php echo $fb; ?>" target="_blank" aria-label="Facebook">📘</a>
                        <?php endif; ?>
                        <?php if ($ig = getSiteSetting('instagram_url')): ?>
                            <a href="<?php echo $ig; ?>" target="_blank" aria-label="Instagram">📷</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/public/rooms.php">Rooms & Rates</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/public/gallery.php">Gallery</a></li>
                        <?php if (getSiteSetting('food_available') === 'yes'): ?>
                        <li><a href="<?php echo SITE_URL; ?>/public/food-services.php">Food Services</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo SITE_URL; ?>/public/about.php">About Us</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/public/contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <ul class="contact-info">
                        <li>📞 <a href="tel:<?php echo getSiteSetting('site_phone'); ?>"><?php echo getSiteSetting('site_phone'); ?></a></li>
                        <?php if ($phone2 = getSiteSetting('site_phone_2')): ?>
                            <li>📞 <a href="tel:<?php echo $phone2; ?>"><?php echo $phone2; ?></a></li>
                        <?php endif; ?>
                        <li>📧 <a href="mailto:<?php echo getSiteSetting('site_email'); ?>"><?php echo getSiteSetting('site_email'); ?></a></li>
                        <li>
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px; fill: currentColor; vertical-align: middle; display: inline-block; margin-right: 4px;">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.386 1.262.617 1.694.789.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.372a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <a href="<?php echo getWhatsAppLink(getSiteSetting('whatsapp_number')); ?>" target="_blank">WhatsApp Us</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo getSiteSetting('site_name', 'Sukhniwas Guest House'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <?php if ($whatsappNumber = getSiteSetting('whatsapp_number')): ?>
    <a href="<?php echo getWhatsAppLink($whatsappNumber); ?>" 
       target="_blank" 
       class="whatsapp-float" 
       aria-label="Chat on WhatsApp"
       title="Chat on WhatsApp">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.386 1.262.617 1.694.789.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.372a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
    <?php endif; ?>

    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

