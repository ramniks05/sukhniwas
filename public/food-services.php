<?php
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Food Services - ' . getSiteSetting('site_name');
$pageDescription = 'Delicious and nutritious meals at ' . getSiteSetting('site_name');

$foodAvailable = getSiteSetting('food_available');
$foodType = getSiteSetting('food_type');
$foodDescription = getSiteSetting('food_description');
$foodTimings = getSiteSetting('food_timings');
$foodPricing = getSiteSetting('food_pricing');
$additionalServices = getSiteSetting('additional_services');

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main">
    <div class="page-header">
        <div class="container">
            <h1>Food Services</h1>
            <p>Delicious and nutritious meals for our guests</p>
        </div>
    </div>

    <section class="food-services-section">
        <div class="container">
            <?php if ($foodAvailable === 'yes'): ?>
                <div class="food-info-card">
                    <div class="food-header">
                        <h2>🍽️ Food & Mess Services</h2>
                        <span class="food-badge available">Available</span>
                    </div>
                    
                    <?php if ($foodDescription): ?>
                    <div class="food-description">
                        <p><?php echo nl2br(htmlspecialchars($foodDescription)); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($foodType): ?>
                    <div class="food-details">
                        <div class="detail-row">
                            <strong>Food Type:</strong>
                            <span class="food-type-badge">
                                <?php 
                                $types = [
                                    'vegetarian' => 'Vegetarian 🥗',
                                    'non-vegetarian' => 'Non-Vegetarian 🍗',
                                    'both' => 'Both (Veg & Non-Veg) 🍛'
                                ];
                                echo $types[$foodType] ?? ucfirst($foodType);
                                ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($foodTimings): ?>
                    <div class="food-timings">
                        <h3>📅 Food Timings</h3>
                        <div class="timings-list">
                            <?php echo nl2br(htmlspecialchars($foodTimings)); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($foodPricing): ?>
                    <div class="food-pricing">
                        <h3>💰 Food Pricing</h3>
                        <div class="pricing-list">
                            <?php echo nl2br(htmlspecialchars($foodPricing)); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>Food services information will be updated soon. Please contact us for details.</p>
                    <a href="contact.php" class="btn btn-primary">Contact Us</a>
                </div>
            <?php endif; ?>
            
            <?php if ($additionalServices): ?>
            <div class="additional-services-card">
                <h3>🛎️ Additional Services & Facilities</h3>
                <div class="services-list">
                    <?php 
                    $services = explode(',', $additionalServices);
                    foreach ($services as $service): 
                        $service = trim($service);
                        if ($service):
                    ?>
                        <div class="service-item">✓ <?php echo htmlspecialchars($service); ?></div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="contact-cta">
                <h3>Have Questions About Food Services?</h3>
                <p>Contact us to know more about our food plans and pricing</p>
                <div class="cta-buttons">
                    <a href="<?php echo getWhatsAppLink(getSiteSetting('whatsapp_number'), 'Hi, I would like to know more about your food services.'); ?>" 
                       target="_blank" 
                       class="btn btn-whatsapp">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.386 1.262.617 1.694.789.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.372a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp Us
                    </a>
                    <a href="contact.php" class="btn btn-outline">Contact Form</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.food-services-section {
    padding: 3rem 0 5rem;
}

.food-info-card {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    padding: 2.5rem;
    box-shadow: var(--shadow-lg);
    margin-bottom: 2rem;
}

.food-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.food-header h2 {
    margin: 0;
    color: var(--text-dark);
}

.food-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.food-badge.available {
    background: linear-gradient(135deg, var(--secondary-color), #fb923c);
    color: white;
}

.food-description {
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-dark);
}

.food-details {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: var(--bg-light);
    border-radius: var(--radius);
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.food-type-badge {
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
}

.food-timings, .food-pricing {
    margin-bottom: 1.5rem;
    padding: 1.5rem;
    background: var(--bg-light);
    border-radius: var(--radius);
}

.food-timings h3, .food-pricing h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: var(--text-dark);
}

.timings-list, .pricing-list {
    font-size: 1.05rem;
    line-height: 2;
    color: var(--text-dark);
}

.additional-services-card {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}

.additional-services-card h3 {
    margin-top: 0;
    margin-bottom: 1.5rem;
    color: var(--text-dark);
}

.services-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.service-item {
    padding: 0.75rem;
    background: var(--bg-light);
    border-radius: var(--radius-sm);
    font-size: 1rem;
}

.contact-cta {
    text-align: center;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    border-radius: var(--radius-lg);
    color: white;
}

.contact-cta h3 {
    margin-top: 0;
    margin-bottom: 0.5rem;
    color: white;
}

.contact-cta p {
    margin-bottom: 2rem;
    font-size: 1.1rem;
    opacity: 0.95;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .food-info-card, .additional-services-card {
        padding: 1.5rem;
    }
    
    .food-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .services-list {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
