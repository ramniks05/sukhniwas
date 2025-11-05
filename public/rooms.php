<?php
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Rooms & Rates - ' . getSiteSetting('site_name');
$pageDescription = 'View all our comfortable rooms and affordable rates. Book your stay today.';

// Get all visible rooms, separated by type
$db = getDB();
$stmt = $db->prepare("SELECT * FROM rooms WHERE is_visible = 1 ORDER BY room_type, price_per_night ASC");
$stmt->execute();
$allRooms = $stmt->fetchAll();

// Separate PG and Guest House rooms
$pgRooms = array_filter($allRooms, function($room) {
    return ($room['room_type'] ?? 'guest_house') === 'pg';
});
$guestHouseRooms = array_filter($allRooms, function($room) {
    return ($room['room_type'] ?? 'guest_house') === 'guest_house';
});

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main">
    <div class="page-header">
        <div class="container">
            <h1>Rooms & Rates</h1>
            <p>Choose from our range of comfortable accommodations</p>
        </div>
    </div>

    <section class="rooms-section">
        <div class="container">
            <?php if (empty($allRooms)): ?>
                <div class="empty-state">
                    <p>No rooms available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <!-- PG Rooms Section -->
                <?php if (!empty($pgRooms)): ?>
                <div class="room-type-section" style="margin-bottom: 4rem;">
                    <h2 class="section-title" style="margin-bottom: 1rem;">PG Rooms (Monthly)</h2>
                    <p class="section-subtitle" style="margin-bottom: 2rem;">Affordable monthly accommodation for students and working professionals</p>
                    <div class="rooms-grid">
                        <?php foreach ($pgRooms as $room): 
                            $amenities = !empty($room['amenities']) ? explode(',', $room['amenities']) : [];
                        ?>
                        <div class="room-card-large">
                            <div class="room-content-large">
                                <?php if (!$room['is_available']): ?>
                                    <div class="unavailable-badge" style="position: relative; top: 0; left: 0; margin-bottom: 1rem;">Not Available</div>
                                <?php endif; ?>
                                <div class="room-header">
                                    <h2><?php echo htmlspecialchars($room['title']); ?></h2>
                                    <div class="room-price"><?php 
                                        $price = $room['price_per_night'] ?? 0;
                                        $pricingType = $room['pricing_type'] ?? 'per_night';
                                        if (($room['room_type'] ?? '') === 'pg' && $pricingType === 'per_night') {
                                            $pricingType = 'per_month';
                                        }
                                        echo formatPrice($price, $pricingType);
                                    ?></div>
                                </div>
                                
                                <p class="room-description"><?php echo htmlspecialchars($room['description']); ?></p>
                                
                                <div class="room-details">
                                    <div class="detail-item">
                                        <span class="detail-icon">👥</span>
                                        <span><?php echo $room['max_occupancy']; ?> Sharing</span>
                                    </div>
                                    <?php if (!empty($room['area_sqft'])): ?>
                                    <div class="detail-item">
                                        <span class="detail-icon">📐</span>
                                        <span><?php echo $room['area_sqft']; ?> sq ft</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($amenities)): ?>
                                <div class="room-amenities">
                                    <h4>Amenities:</h4>
                                    <div class="amenities-list">
                                        <?php foreach ($amenities as $amenity): ?>
                                            <span class="amenity-tag"><?php echo htmlspecialchars(trim($amenity)); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="room-actions-full">
                                    <a href="room-details.php?slug=<?php echo $room['slug']; ?>" class="btn btn-primary">View Details</a>
                                    <a href="contact.php?room=<?php echo $room['id']; ?>" class="btn btn-outline">Book Now</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Guest House Rooms Section -->
                <?php if (!empty($guestHouseRooms)): ?>
                <div class="room-type-section">
                    <h2 class="section-title" style="margin-bottom: 1rem;">Guest House Rooms (Per Night)</h2>
                    <p class="section-subtitle" style="margin-bottom: 2rem;">Comfortable rooms for short-term stays</p>
                    <div style="text-align: center; margin-bottom: 2rem;">
                        <a href="guest-house-pricing.php" class="btn btn-primary">View Guest House Pricing</a>
                    </div>
                    <div class="rooms-grid">
                        <?php foreach ($guestHouseRooms as $room):
                            $amenities = !empty($room['amenities']) ? explode(',', $room['amenities']) : [];
                        ?>
                        <div class="room-card-large">
                            <div class="room-content-large">
                                <?php if (!$room['is_available']): ?>
                                    <div class="unavailable-badge" style="position: relative; top: 0; left: 0; margin-bottom: 1rem;">Not Available</div>
                                <?php endif; ?>
                                <div class="room-header">
                                    <h2><?php echo htmlspecialchars($room['title']); ?></h2>
                                    <div class="room-price"><?php 
                                        $price = $room['price_per_night'] ?? 0;
                                        $pricingType = $room['pricing_type'] ?? 'per_night';
                                        if (($room['room_type'] ?? '') === 'pg' && $pricingType === 'per_night') {
                                            $pricingType = 'per_month';
                                        }
                                        echo formatPrice($price, $pricingType);
                                    ?></div>
                                </div>
                                
                                <p class="room-description"><?php echo htmlspecialchars($room['description']); ?></p>
                                
                                <div class="room-details">
                                    <div class="detail-item">
                                        <span class="detail-icon">👥</span>
                                        <span>Max <?php echo $room['max_occupancy']; ?> guests</span>
                                    </div>
                                </div>
                                
                                <?php if (!empty($amenities)): ?>
                                <div class="room-amenities">
                                    <h4>Amenities:</h4>
                                    <div class="amenities-list">
                                        <?php foreach ($amenities as $amenity): ?>
                                            <span class="amenity-tag"><?php echo htmlspecialchars(trim($amenity)); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="room-actions-full">
                                    <a href="room-details.php?slug=<?php echo $room['slug']; ?>" class="btn btn-primary">View Details</a>
                                    <a href="contact.php?room=<?php echo $room['id']; ?>" class="btn btn-outline">Book Now</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Have Questions?</h2>
                <p>Contact us for more information or to book your stay</p>
                <div class="cta-buttons" style="justify-content: center; gap: 1rem; flex-wrap: wrap;">
                    <a href="guest-house-pricing.php" class="btn btn-primary">Book Guest House</a>
                    <a href="<?php echo getWhatsAppLink(getSiteSetting('whatsapp_number')); ?>" target="_blank" class="btn btn-whatsapp">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.386 1.262.617 1.694.789.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.372a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Chat on WhatsApp
                    </a>
                    <a href="contact.php" class="btn btn-outline-light">Contact Us</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

