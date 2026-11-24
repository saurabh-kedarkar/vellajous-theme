<?php
/**
 * Template Name: Contact Us Page
 * Description: Bespoke luxury Contact Us page for Vellajous Haute Parfumerie.
 *
 * @package vellajous
 */

get_header();
?>

<main id="primary" class="site-main vellajous-contact-page">

	<!-- ======================================================================
	     HERO BANNER
	     ====================================================================== -->
	<section class="vellajous-page-hero">
		<div class="vellajous-page-hero__bg">
			<div class="vellajous-page-hero__pattern"></div>
		</div>
		<div class="container vellajous-page-hero__content">
			<nav class="vellajous-breadcrumbs" aria-label="<?php esc_attr_e('Breadcrumbs', 'vellajous'); ?>">
				<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'vellajous'); ?></a>
				<span class="breadcrumb-separator">/</span>
				<span class="breadcrumb-current"><?php esc_html_e('Contact Us', 'vellajous'); ?></span>
			</nav>

			<h1 class="vellajous-page-hero__title">
				<?php esc_html_e('WE ARE AT YOUR SERVICE', 'vellajous'); ?>
			</h1>

			<p class="vellajous-page-hero__subtitle">
				<?php esc_html_e('Whether you seek personal fragrance recommendations, order assistance, or bespoke corporate and bridal gifting, our private olfactory concierge is dedicated to your inquiries.', 'vellajous'); ?>
			</p>
		</div>
	</section>

	<!-- ======================================================================
	     MAIN CONCIERGE & CONTACT SECTION (2-Column Grid)
	     ====================================================================== -->
	<section class="section section--white vellajous-contact-section">
		<div class="container">
			<div class="vellajous-contact-grid">
				
				<!-- Left Column: Direct Concierge Touchpoints -->
				<div class="vellajous-contact-info">
					<div class="contact-info-header">
						<span class="section-header__kicker"><?php esc_html_e('DIRECT CHANNELS', 'vellajous'); ?></span>
						<h2 class="contact-info__heading"><?php esc_html_e('Connect With Our Private Concierge', 'vellajous'); ?></h2>
						<p class="contact-info__desc">
							<?php esc_html_e('Every client inquiry receives personal, attentive care from our in-house fragrance advisors.', 'vellajous'); ?>
						</p>
					</div>

					<!-- Direct Touchpoints Cards -->
					<div class="contact-cards-list">
						<!-- Card 1: Client Support & Orders -->
						<div class="contact-card">
							<div class="contact-card__icon">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
									<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
								</svg>
							</div>
							<div class="contact-card__details">
								<h3 class="contact-card__title"><?php esc_html_e('Client Care & Orders', 'vellajous'); ?></h3>
								<p class="contact-card__text"><?php esc_html_e('Direct phone & WhatsApp assistance for orders, dispatch status & returns.', 'vellajous'); ?></p>
								<div class="contact-card__links">
									<a href="tel:+919876543210" class="contact-link">+91 98765 43210</a>
									<span class="link-divider">·</span>
									<a href="mailto:support@vellajous.com" class="contact-link">support@vellajous.com</a>
								</div>
								<span class="contact-card__hours"><?php esc_html_e('Mon – Sat: 10:00 AM – 7:00 PM IST', 'vellajous'); ?></span>
							</div>
						</div>

						<!-- Card 2: WhatsApp VIP Concierge -->
						<div class="contact-card contact-card--highlight">
							<div class="contact-card__icon">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
									<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
								</svg>
							</div>
							<div class="contact-card__details">
								<h3 class="contact-card__title"><?php esc_html_e('Instant WhatsApp Concierge', 'vellajous'); ?></h3>
								<p class="contact-card__text"><?php esc_html_e('Receive instant fragrance advice, scent matching, or order updates in seconds.', 'vellajous'); ?></p>
								<a href="https://wa.me/919876543210?text=Hello%20Vellajous%20Concierge,%20I%20would%20like%20assistance%20with%20your%20fragrances" 
								   target="_blank" 
								   rel="noopener noreferrer" 
								   class="vellajous-btn vellajous-btn--whatsapp">
									<span><?php esc_html_e('Chat on WhatsApp', 'vellajous'); ?></span>
									<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
								</a>
							</div>
						</div>

						<!-- Card 3: Flagship Boutiques -->
						<div class="contact-card">
							<div class="contact-card__icon">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
									<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
									<circle cx="12" cy="10" r="3"></circle>
								</svg>
							</div>
							<div class="contact-card__details">
								<h3 class="contact-card__title"><?php esc_html_e('Experience Boutiques & Ateliers', 'vellajous'); ?></h3>
								<div class="boutique-location">
									<strong><?php esc_html_e('Mumbai Flagship Atelier:', 'vellajous'); ?></strong>
									<span><?php esc_html_e('Linking Road, Bandra West, Mumbai 400050', 'vellajous'); ?></span>
								</div>
								<div class="boutique-location">
									<strong><?php esc_html_e('New Delhi Gallery:', 'vellajous'); ?></strong>
									<span><?php esc_html_e('Chanakyapuri Luxury Enclave, New Delhi 110021', 'vellajous'); ?></span>
								</div>
								<div class="boutique-location">
									<strong><?php esc_html_e('Dubai Private Suite:', 'vellajous'); ?></strong>
									<span><?php esc_html_e('Downtown Boulevard, Dubai, UAE', 'vellajous'); ?></span>
								</div>
							</div>
						</div>

						<!-- Card 4: Corporate & Bridal Gifting -->
						<div class="contact-card">
							<div class="contact-card__icon">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
									<polyline points="20 12 20 22 4 22 4 12"></polyline>
									<rect x="2" y="7" width="20" height="5"></rect>
									<line x1="12" y1="22" x2="12" y2="7"></line>
									<path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
									<path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
								</svg>
							</div>
							<div class="contact-card__details">
								<h3 class="contact-card__title"><?php esc_html_e('Corporate & Bridal Curation', 'vellajous'); ?></h3>
								<p class="contact-card__text"><?php esc_html_e('Bespoke laser bottle engraving, customized wax-sealed velvet vaults, and curated VIP gifts for luxury weddings and executive events.', 'vellajous'); ?></p>
								<a href="mailto:concierge@vellajous.com?subject=Corporate%20Gifting%20Inquiry" class="contact-link">concierge@vellajous.com</a>
							</div>
						</div>
					</div>
				</div>

				<!-- Right Column: Interactive Luxury Contact Form -->
				<div class="vellajous-contact-form-wrap">
					<div class="luxury-form-card">
						<div class="luxury-form-card__header">
							<span class="form-kicker"><?php esc_html_e('TRANSMIT YOUR INQUIRY', 'vellajous'); ?></span>
							<h3 class="form-title"><?php esc_html_e('Send A Note to the Atelier', 'vellajous'); ?></h3>
							<p class="form-subtitle">
								<?php esc_html_e('Kindly submit your request below. An olfactory consultant will respond directly within four business hours.', 'vellajous'); ?>
							</p>
						</div>

						<form id="vellajousContactForm" class="vellajous-form" method="post">
							<?php wp_nonce_field('vellajous_contact_nonce', 'vellajous_contact_nonce_field'); ?>

							<div class="form-row form-row--two">
								<div class="form-group">
									<label for="contact_name"><?php esc_html_e('Full Name', 'vellajous'); ?> <span class="req">*</span></label>
									<input type="text" id="contact_name" name="contact_name" required placeholder="<?php esc_attr_e('e.g. Lord Sterling', 'vellajous'); ?>" class="form-control" />
								</div>
								<div class="form-group">
									<label for="contact_email"><?php esc_html_e('Email Address', 'vellajous'); ?> <span class="req">*</span></label>
									<input type="email" id="contact_email" name="contact_email" required placeholder="<?php esc_attr_e('e.g. sterling@domain.com', 'vellajous'); ?>" class="form-control" />
								</div>
							</div>

							<div class="form-row form-row--two">
								<div class="form-group">
									<label for="contact_phone"><?php esc_html_e('Phone Number', 'vellajous'); ?></label>
									<input type="tel" id="contact_phone" name="contact_phone" placeholder="<?php esc_attr_e('+91 98765 43210', 'vellajous'); ?>" class="form-control" />
								</div>
								<div class="form-group">
									<label for="contact_reason"><?php esc_html_e('Nature of Inquiry', 'vellajous'); ?> <span class="req">*</span></label>
									<select id="contact_reason" name="contact_reason" required class="form-control form-select">
										<option value="General"><?php esc_html_e('General Product Inquiry', 'vellajous'); ?></option>
										<option value="Order Tracking"><?php esc_html_e('Order Status & Dispatch Tracking', 'vellajous'); ?></option>
										<option value="Fragrance Consultation"><?php esc_html_e('Bespoke Fragrance Consultation', 'vellajous'); ?></option>
										<option value="Bridal & Corporate Gifting"><?php esc_html_e('Bridal & Corporate Bulk Gifting', 'vellajous'); ?></option>
										<option value="Boutique Appointment"><?php esc_html_e('Boutique Appointment Request', 'vellajous'); ?></option>
										<option value="Wholesale / Distribution"><?php esc_html_e('Wholesale & Distribution Partnership', 'vellajous'); ?></option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="contact_subject"><?php esc_html_e('Subject', 'vellajous'); ?> <span class="req">*</span></label>
								<input type="text" id="contact_subject" name="contact_subject" required placeholder="<?php esc_attr_e('Inquiry regarding Royal Amber extrait...', 'vellajous'); ?>" class="form-control" />
							</div>

							<div class="form-group">
								<label for="contact_message"><?php esc_html_e('Your Message / Requirements', 'vellajous'); ?> <span class="req">*</span></label>
								<textarea id="contact_message" name="contact_message" rows="5" required placeholder="<?php esc_attr_e('Please share your request, scent preferences, or specific inquiry in detail...', 'vellajous'); ?>" class="form-control form-textarea"></textarea>
							</div>

							<button type="submit" class="vellajous-btn vellajous-btn--primary vellajous-btn--block submit-btn" id="contactSubmitBtn">
								<span class="btn-text"><?php esc_html_e('SEND CONCIERGE MESSAGE', 'vellajous'); ?></span>
								<svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
									<line x1="5" y1="12" x2="19" y2="12"></line>
									<polyline points="12 5 19 12 12 19"></polyline>
								</svg>
								<span class="btn-spinner" style="display:none;"></span>
							</button>

							<!-- Feedback Message -->
							<div id="contactFormNotice" class="form-notice" style="display:none;"></div>
						</form>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- ======================================================================
	     SECTION 3: FREQUENTLY ASKED QUESTIONS (Accordion)
	     ====================================================================== -->
	<section class="section section--alt vellajous-faq-section">
		<div class="container">
			<div class="section-header">
				<span class="section-header__kicker"><?php esc_html_e('DISCERNING INQUIRIES', 'vellajous'); ?></span>
				<h2 class="section-header__title"><?php esc_html_e('FREQUENTLY ASKED QUESTIONS', 'vellajous'); ?></h2>
				<p class="section-header__desc">
					<?php esc_html_e('Find immediate answers to questions regarding our artisanal formulations, longevity, delivery, and orders.', 'vellajous'); ?>
				</p>
			</div>

			<div class="vellajous-faq-accordion">
				<!-- Item 1 -->
				<div class="faq-item">
					<button type="button" class="faq-question">
						<span><?php esc_html_e('What makes Vellajous formulations so pure and unique?', 'vellajous'); ?></span>
						<span class="faq-icon">+</span>
					</button>
					<div class="faq-answer">
						<p><?php esc_html_e('Every Vellajous formulation is crafted with precision, using pure micro-emulsion aqua matrices and concentrated botanical oils that hydrate and respect your skin while providing opulent scent projection.', 'vellajous'); ?></p>
					</div>
				</div>

				<!-- Item 2 -->
				<div class="faq-item">
					<button type="button" class="faq-question">
						<span><?php esc_html_e('How do water-based perfumes perform for 12+ hours with deep sillage?', 'vellajous'); ?></span>
						<span class="faq-icon">+</span>
					</button>
					<div class="faq-answer">
						<p><?php esc_html_e('Traditional alcohol evaporates in minutes, causing a fast burst followed by rapid decay. Our water micro-emulsion allows heavy aromatic molecules (like oud, amber, and sandalwood) to anchor securely to skin proteins, releasing slowly as your body heat activates them across 12 to 16 hours.', 'vellajous'); ?></p>
					</div>
				</div>

				<!-- Item 3 -->
				<div class="faq-item">
					<button type="button" class="faq-question">
						<span><?php esc_html_e('What is the delivery timeline across India?', 'vellajous'); ?></span>
						<span class="faq-icon">+</span>
					</button>
					<div class="faq-answer">
						<p><?php esc_html_e('All orders are fulfilled via insured express air courier. Metro deliveries (Mumbai, Delhi NCR, Bengaluru, Hyderabad, Chennai) arrive within 2 to 3 business days. Rest of India arrives within 3 to 5 business days. You will receive an SMS and WhatsApp tracking link as soon as your vault dispatches.', 'vellajous'); ?></p>
					</div>
				</div>

				<!-- Item 4 -->
				<div class="faq-item">
					<button type="button" class="faq-question">
						<span><?php esc_html_e('Can I order custom engraved bottles for weddings or corporate gifts?', 'vellajous'); ?></span>
						<span class="faq-icon">+</span>
					</button>
					<div class="faq-answer">
						<p><?php esc_html_e('Yes. Our atelier offers personalized gold laser bottle engraving (monograms, names, dates) and bespoke emerald gift packaging for orders of 10 or more flacons. Please contact our VIP Gifting Concierge at concierge@vellajous.com for customized catalogs and pricing.', 'vellajous'); ?></p>
					</div>
				</div>

				<!-- Item 5 -->
				<div class="faq-item">
					<button type="button" class="faq-question">
						<span><?php esc_html_e('What is your exchange and return policy?', 'vellajous'); ?></span>
						<span class="faq-icon">+</span>
					</button>
					<div class="faq-answer">
						<p><?php esc_html_e('Due to the artisanal and personal nature of luxury perfumery, opened bottles cannot be returned. However, in the rare event that your presentation vault arrives damaged or in transit error, we provide an immediate no-questions-asked replacement within 7 days of delivery.', 'vellajous'); ?></p>
					</div>
				</div>
			</div>
		</div>
	</section>

</main><!-- #primary -->

<?php
get_footer();
