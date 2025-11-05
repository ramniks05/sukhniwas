<?php
require_once __DIR__ . '/../config/config.php';

$pageTitle = 'Guest House Pricing - ' . getSiteSetting('site_name');
$pageDescription = 'Affordable guest house rooms starting at ₹1200 per night. Book your comfortable stay today.';

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main">
    <div class="page-header">
        <div class="container">
            <h1>Guest House Pricing</h1>
            <p>Comfortable rooms for short-term stays</p>
        </div>
    </div>

    <section class="rooms-section">
        <div class="container">
            <div class="pricing-section">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h2>Guest House Rooms</h2>
                        <p class="pricing-subtitle">Perfect for short-term stays</p>
                    </div>
                    
                    <div class="pricing-main">
                        <div class="price-display">
                            <span class="currency">₹</span>
                            <span class="amount">1,200</span>
                            <span class="period">per night</span>
                        </div>
                    </div>

                    <div class="pricing-features">
                        <h3>What's Included:</h3>
                        <ul class="features-list">
                            <li>✅ Clean and comfortable rooms</li>
                            <li>✅ Air conditioning</li>
                            <li>✅ Attached bathroom</li>
                            <li>✅ 24/7 security</li>
                            <li>✅ WiFi connectivity</li>
                            <li>✅ Housekeeping service</li>
                            <li>✅ Room service available</li>
                        </ul>
                    </div>

                    <div class="pricing-actions">
                        <a href="contact.php" class="btn btn-primary btn-large">Book Now / Enquire</a>
                        <a href="<?php echo getWhatsAppLink(getSiteSetting('whatsapp_number'), 'Hi, I am interested in booking a guest house room.'); ?>" 
                           target="_blank" 
                           class="btn btn-whatsapp btn-large">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.386 1.262.617 1.694.789.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.372a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Contact on WhatsApp
                        </a>
                    </div>

                    <div class="pricing-note">
                        <p><strong>Note:</strong> Prices are subject to availability. For special rates on extended stays, please contact us.</p>
                    </div>
                </div>

                <div class="pricing-info">
                    <h3>Why Choose Our Guest House?</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">🏨</div>
                            <h4>Comfortable Stay</h4>
                            <p>Well-maintained rooms with modern amenities for a comfortable experience.</p>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">📍</div>
                            <h4>Prime Location</h4>
                            <p>Conveniently located with easy access to major areas and transportation.</p>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">💰</div>
                            <h4>Affordable Rates</h4>
                            <p>Best value for money with transparent pricing and no hidden charges.</p>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">🛡️</div>
                            <h4>Safe & Secure</h4>
                            <p>24/7 security and CCTV surveillance for your peace of mind.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.pricing-section {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 0;
}

.pricing-card {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    padding: 3rem;
    box-shadow: var(--shadow-xl);
    margin-bottom: 3rem;
    text-align: center;
}

.pricing-header h2 {
    font-size: 2.5rem;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.pricing-subtitle {
    color: var(--text-light);
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.pricing-main {
    margin: 2rem 0;
}

.price-display {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 0.5rem;
    margin: 2rem 0;
}

.price-display .currency {
    font-size: 2rem;
    color: var(--primary-color);
    font-weight: 600;
}

.price-display .amount {
    font-size: 4rem;
    font-weight: 700;
    color: var(--primary-color);
    line-height: 1;
}

.price-display .period {
    font-size: 1.2rem;
    color: var(--text-light);
    font-weight: 500;
}

.pricing-features {
    text-align: left;
    margin: 2rem 0;
    padding: 2rem;
    background: var(--bg-light);
    border-radius: var(--radius);
}

.pricing-features h3 {
    font-size: 1.5rem;
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.features-list li {
    padding: 0.75rem 0;
    font-size: 1.1rem;
    color: var(--text-dark);
    border-bottom: 1px solid var(--border-color);
}

.features-list li:last-child {
    border-bottom: none;
}

.pricing-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.pricing-actions .btn {
    min-width: 200px;
}

.pricing-note {
    margin-top: 2rem;
    padding: 1rem;
    background: rgba(13, 148, 136, 0.1);
    border-radius: var(--radius);
    border-left: 4px solid var(--primary-color);
}

.pricing-note p {
    margin: 0;
    color: var(--text-dark);
    font-size: 0.95rem;
}

.pricing-info {
    margin-top: 3rem;
}

.pricing-info h3 {
    font-size: 2rem;
    text-align: center;
    color: var(--text-dark);
    margin-bottom: 2rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.info-item {
    text-align: center;
    padding: 1.5rem;
    background: var(--bg-white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-md);
    transition: var(--transition);
}

.info-item:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.info-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.info-item h4 {
    font-size: 1.25rem;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.info-item p {
    color: var(--text-light);
    font-size: 0.95rem;
    margin: 0;
}

@media (max-width: 768px) {
    .pricing-card {
        padding: 2rem 1.5rem;
    }

    .pricing-header h2 {
        font-size: 2rem;
    }

    .price-display .amount {
        font-size: 3rem;
    }

    .pricing-actions {
        flex-direction: column;
    }

    .pricing-actions .btn {
        width: 100%;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>

