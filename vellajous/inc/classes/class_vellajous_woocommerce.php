<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * WooCommerce Integration for Vellajous
 *
 * @package vellajous
 */
if (!class_exists('class_vellajous_woocommerce')) {
    class class_vellajous_woocommerce
    {
        private static $instance = null;

        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        protected function __construct()
        {
            $this->init_hooks();
        }

        private function init_hooks()
        {
            // Currency formatting (Rs. format as in reference)
            add_filter('woocommerce_currency_symbol', [$this, 'custom_currency_symbol'], 10, 2);

            // Remove default WooCommerce wrappers and breadcrumbs for clean luxury theme layouts
            remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
            remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
            remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
            remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

            // Set 8 products per page for Shop and Category archives
            add_filter('loop_shop_per_page', function ($cols) {
                return 8;
            }, 20);
            add_action('pre_get_posts', [$this, 'set_products_per_page']);

            // AJAX Load More Products
            add_action('wp_ajax_vellajous_load_more_products', [$this, 'ajax_load_more_products']);
            add_action('wp_ajax_nopriv_vellajous_load_more_products', [$this, 'ajax_load_more_products']);

            // AJAX Quick View
            add_action('wp_ajax_vellajous_quick_view', [$this, 'ajax_quick_view']);
            add_action('wp_ajax_nopriv_vellajous_quick_view', [$this, 'ajax_quick_view']);

            // AJAX Add to Cart fragments
            add_filter('woocommerce_add_to_cart_fragments', [$this, 'cart_fragments']);

            // Mini cart drawer update via AJAX
            add_action('wp_ajax_vellajous_get_mini_cart', [$this, 'ajax_get_mini_cart']);
            add_action('wp_ajax_nopriv_vellajous_get_mini_cart', [$this, 'ajax_get_mini_cart']);

            // AJAX Contact Form Submission
            add_action('wp_ajax_vellajous_submit_contact', [$this, 'ajax_submit_contact']);
            add_action('wp_ajax_nopriv_vellajous_submit_contact', [$this, 'ajax_submit_contact']);

            // AJAX Wishlist Items Retrieval
            add_action('wp_ajax_vellajous_get_wishlist_items', [$this, 'ajax_get_wishlist_items']);
            add_action('wp_ajax_nopriv_vellajous_get_wishlist_items', [$this, 'ajax_get_wishlist_items']);

            // Quick View trigger filters for Cart Page items
            add_filter('woocommerce_cart_item_name', [$this, 'filter_cart_item_name'], 10, 3);
            add_filter('woocommerce_cart_item_thumbnail', [$this, 'filter_cart_item_thumbnail'], 10, 3);

            // Order PDF Download and Printable Invoice Handler
            add_action('template_redirect', [$this, 'handle_order_pdf_request']);
        }

        /**
         * Add Quick View attributes to cart page item links
         */
        public function filter_cart_item_name($name, $cart_item, $cart_item_key)
        {
            if (!empty($cart_item['product_id'])) {
                $name = str_replace('<a ', '<a class="vellajous-cart-product-link" data-product-id="' . esc_attr($cart_item['product_id']) . '" ', $name);
            }
            return $name;
        }

        public function filter_cart_item_thumbnail($thumbnail, $cart_item, $cart_item_key)
        {
            if (!empty($cart_item['product_id'])) {
                $thumbnail = str_replace('<a ', '<a class="vellajous-cart-product-link" data-product-id="' . esc_attr($cart_item['product_id']) . '" ', $thumbnail);
            }
            return $thumbnail;
        }

        /**
         * Use Rs. currency symbol to match naseemperfume.in exactly
         */
        public function custom_currency_symbol($symbol, $currency)
        {
            if ($currency === 'INR') {
                return 'Rs. ';
            }
            return $symbol;
        }

        /**
         * Get curated scent profile metadata for Fragrance Quiz
         */
        public static function get_product_scent_profile($product_id)
        {
            $profiles = [
                84 => ['persona' => 'business design', 'mood' => 'focus calm'],                 // Uniform
                85 => ['persona' => 'design travel', 'mood' => 'bold energy'],                  // Rough draft
                86 => ['persona' => 'healthcare business', 'mood' => 'calm energy'],            // Goodwill
                87 => ['persona' => 'business travel', 'mood' => 'bold energy'],                 // Charge
                57 => ['persona' => 'business travel design', 'mood' => 'bold energy'],          // AMETHYST OUD
                21 => ['persona' => 'business travel design', 'mood' => 'energy focus bold'],    // ASMIRA
                27 => ['persona' => 'business travel design', 'mood' => 'energy bold'],          // SUBLIME OUD
                24 => ['persona' => 'healthcare design travel', 'mood' => 'focus calm'],         // Eclipse
                54 => ['persona' => 'business design', 'mood' => 'bold energy'],                 // ROYAL AMBER
                55 => ['persona' => 'healthcare design travel', 'mood' => 'calm focus'],         // VELVET ROSE
                56 => ['persona' => 'healthcare business', 'mood' => 'calm focus'],              // CELESTIAL MUSK
                38 => ['persona' => 'healthcare travel design', 'mood' => 'calm focus'],         // DIVINE
                30 => ['persona' => 'business design', 'mood' => 'bold energy'],                 // OUD
                41 => ['persona' => 'business travel design', 'mood' => 'bold energy'],          // LUXE COLLECTION
                35 => ['persona' => 'healthcare business travel', 'mood' => 'calm focus'],        // MUSK TAHARA GIFT SET
                32 => ['persona' => 'healthcare business travel design', 'mood' => 'energy calm focus'], // LAMSA GIFT SET
            ];

            return $profiles[$product_id] ?? ['persona' => 'business healthcare design travel', 'mood' => 'calm focus energy bold'];
        }

        /**
         * Render a luxury perfume product card
         */
        public static function get_category_url($slug, $fallback_name = '')
        {
            $term = get_term_by('slug', $slug, 'product_cat');
            if (!$term && $fallback_name) {
                $term = get_term_by('name', $fallback_name, 'product_cat');
            }
            if ($term && !is_wp_error($term)) {
                $link = get_term_link($term);
                if ($link && !is_wp_error($link)) {
                    return $link;
                }
            }
            return home_url('/product-category/' . $slug . '/');
        }

        public static function get_product_hover_image($product)
        {
            if (!$product) {
                return '';
            }

            $id = $product->get_id();
            $main_img_id = $product->get_image_id();
            $main_img_url = $main_img_id ? wp_get_attachment_image_url($main_img_id, 'large') : wc_placeholder_img_src('large');

            // 1. Check WooCommerce product gallery IDs
            $gallery_ids = $product->get_gallery_image_ids();
            if (!empty($gallery_ids)) {
                foreach ($gallery_ids as $gid) {
                    if ($gid && (int)$gid !== (int)$main_img_id) {
                        $gurl = wp_get_attachment_image_url($gid, 'large');
                        if (!empty($gurl)) {
                            return $gurl;
                        }
                    }
                }
            }

            // 2. Pattern detection: If main image has _01, check for _02 in uploads
            if (!empty($main_img_url)) {
                $upload_dir = wp_upload_dir();
                $base_url = $upload_dir['baseurl'];
                $base_dir = $upload_dir['basedir'];

                if (strpos($main_img_url, $base_url) !== false) {
                    $rel_path = str_replace($base_url, '', $main_img_url);
                    // Match pattern like /2026/11/GOODWILL_01-1024x1024.jpg or GOODWILL_01.jpg
                    if (preg_match('/(.*?_01)(?:-\d+x\d+)?(\.[a-zA-Z0-9]+)$/i', $rel_path, $m)) {
                        $candidate_rel = str_replace('_01', '_02', $m[1]) . $m[2];
                        if (file_exists($base_dir . $candidate_rel)) {
                            return $base_url . $candidate_rel;
                        }
                    }
                }

                // 3. Known product slug/name to lifestyle image mapping
                $slug = $product->get_slug();
                $name = strtolower($product->get_name());
                $known_map = [
                    'goodwill'     => '/2026/11/GOODWILL_02.jpg',
                    'uniform'      => '/2026/11/UNIFORM_02.jpg',
                    'rough-draft'  => '/2026/11/ROUGH_DRAFT_02.jpg',
                    'rough draft'  => '/2026/11/ROUGH_DRAFT_02.jpg',
                    'offline'      => '/2026/11/OFFLINE_02.jpg',
                    'no-map'       => '/2026/11/NO_MAP_02.jpg',
                    'no map'       => '/2026/11/NO_MAP_02.jpg',
                    'asmira'       => '/2026/10/ASMIRA__story_1.jpg',
                    'sublime-oud'  => '/2026/10/SUBLIME_OUD_4.jpg',
                    'sublime oud'  => '/2026/10/SUBLIME_OUD_4.jpg',
                    'eclipse'      => '/2026/10/eclipse_new_2246abcc-1595-4357-8d92-097651bfda5d.jpg',
                    'oud'          => '/2026/10/Oud_Bottle_600x600px_1.jpg',
                ];

                foreach ($known_map as $key => $rel) {
                    if ((strpos($slug, $key) !== false || strpos($name, $key) !== false) && file_exists($base_dir . $rel)) {
                        return $base_url . $rel;
                    }
                }
            }

            // 4. Default fallback: full bleed main image on hover
            return $main_img_url;
        }

        public static function render_product_card($product_id_or_obj)
        {
            $product = is_numeric($product_id_or_obj) ? wc_get_product($product_id_or_obj) : $product_id_or_obj;
            if (!$product) {
                return '';
            }

            $id = $product->get_id();
            $title = $product->get_name();
            $permalink = $product->get_permalink();
            $is_in_stock = $product->is_in_stock();
            $is_on_sale = '';
            $scent_profile = self::get_product_scent_profile($id);
            $brand = get_post_meta($id, '_brand', true);
            if (empty($brand)) {
                $terms = get_the_terms($id, 'product_cat');
                $brand = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'VELLAJOUS PERFUME';
            }
            if (empty($brand) || strtolower(trim($brand)) === 'uncategorized') {
                $brand = 'VELLAJOUS HAUTE PARFUMERIE';
            }

            // Main image
            $main_img_id = $product->get_image_id();
            $main_img_url = $main_img_id ? wp_get_attachment_image_url($main_img_id, 'large') : wc_placeholder_img_src('large');

            // Hover image: check gallery, auto-detect paired lifestyle photo, or use full-bleed fallback
            $hover_img_url = self::get_product_hover_image($product);
            $has_hover = !empty($hover_img_url);

            ob_start();
            ?>
            <div class="vellajous-card <?php echo $is_in_stock ? 'is-instock' : 'is-soldout'; ?> <?php echo $has_hover ? 'has-hover-image' : ''; ?>" 
                 data-product-id="<?php echo esc_attr($id); ?>"
                 data-persona="<?php echo esc_attr($scent_profile['persona']); ?>"
                 data-mood="<?php echo esc_attr($scent_profile['mood']); ?>">
                <div class="vellajous-card__media-wrapper">
                    <a href="<?php echo esc_url($permalink); ?>" class="vellajous-card__image-link" tabindex="-1">
                        <div class="vellajous-card__image-container <?php echo $has_hover ? 'has-hover-effect' : ''; ?>">
                            <img src="<?php echo esc_url($main_img_url); ?>" 
                                 alt="<?php echo esc_attr($title); ?>" 
                                 class="vellajous-card__img vellajous-card__img--primary" 
                                 loading="lazy" />
                            <?php if ($has_hover) : ?>
                                <img src="<?php echo esc_url($hover_img_url); ?>" 
                                     alt="<?php echo esc_attr($title); ?>" 
                                     class="vellajous-card__img vellajous-card__img--hover" 
                                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" 
                                     loading="lazy" />
                            <?php endif; ?>
                        </div>
                    </a>

                    <!-- Wishlist Floating Heart Button -->
                    <button type="button" 
                            class="vellajous-wishlist-btn vellajous-card__wishlist-btn" 
                            data-product-id="<?php echo esc_attr($id); ?>" 
                            data-product-title="<?php echo esc_attr($title); ?>"
                            title="<?php esc_attr_e('Add to Wishlist', 'vellajous'); ?>" 
                            aria-label="<?php echo esc_attr(sprintf(__('Add %s to wishlist', 'vellajous'), $title)); ?>">
                        <svg class="wishlist-icon" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </button>

                    <!-- Top Right Quick View Floating Circle Icon -->
                    <button type="button" class="vellajous-quickview-btn" data-product-id="<?php echo esc_attr($id); ?>" title="<?php esc_attr_e('Quick View', 'vellajous'); ?>" aria-label="<?php echo esc_attr(sprintf(__('Quick view %s', 'vellajous'), $title)); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" class="icon icon-search" fill="none" viewBox="0 0 15 17">
                            <circle cx="7.11113" cy="7.11113" r="6.56113" stroke="currentColor" stroke-width="1.2" fill="none"/>
                            <path d="M11.078 12.3282L13.8878 16.0009" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" fill="none"/>
                        </svg>
                    </button>


                    <!-- Sold out bar overlay at bottom of image (matches screenshot) -->
                    <?php if (!$is_in_stock) : ?>
                        <div class="vellajous-card__soldout-overlay">
                            <span><?php esc_html_e('NOTIFY ME', 'vellajous'); ?></span>
                        </div>
                    <?php elseif ($is_on_sale) : ?>
                        <div class="vellajous-card__sale-badge">
                            <span><?php esc_html_e('SALE', 'vellajous'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="vellajous-card__details">
                    <!-- <div class="vellajous-card__brand"><?php echo esc_html($brand); ?></div> -->
                    <h3 class="vellajous-card__title">
                        <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                    </h3>
                    <div class="vellajous-card__meta">
                        <div class="vellajous-card__price">
                            <?php echo $product->get_price_html(); ?>
                        </div>
                        <div class="vellajous-card__rating">
                            <span class="rating-stars">★★★★★</span>
                            <span class="rating-count">(4.9)</span>
                        </div>
                    </div>

                    <div class="vellajous-card__actions">
                        <?php if ($is_in_stock) : ?>
                            <?php if ($product->is_type('simple')) : ?>
                                <button type="button" 
                                    class="vellajous-btn vellajous-btn--quick-add add_to_cart_button ajax_add_to_cart" 
                                    data-product_id="<?php echo esc_attr($id); ?>" 
                                    data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                                    data-quantity="1"
                                    rel="nofollow"
                                    aria-label="<?php echo esc_attr(sprintf(__('Add %s to cart', 'vellajous'), $title)); ?>">
                                <span class="btn-text"><?php esc_html_e('Add to Cart', 'vellajous'); ?></span>
                                <span class="btn-loading"><span class="spinner"></span></span>
                            </button>
                            <?php else : ?>
                                <a href="<?php echo esc_url($permalink); ?>" class="vellajous-btn vellajous-btn--quick-add">
                                    <?php esc_html_e('Select Options', 'vellajous'); ?>
                                </a>
                            <?php endif; ?>
                        <?php else : ?>
                            <button type="button" class="vellajous-btn vellajous-btn--soldout">
                                <?php esc_html_e('Notify Me', 'vellajous'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        /**
         * AJAX handler for Quick View modal
         */
        public function ajax_quick_view()
        {
            if (!check_ajax_referer('vellajous_ajax_nonce', 'nonce', false)) {
                wp_send_json_error(['message' => __('Security check failed.', 'vellajous')]);
            }

            $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
            if (!$product_id) {
                wp_send_json_error(['message' => __('Invalid product.', 'vellajous')]);
            }

            $product = wc_get_product($product_id);
            if (!$product) {
                wp_send_json_error(['message' => __('Product not found.', 'vellajous')]);
            }

            $title = $product->get_name();
            $price_html = $product->get_price_html();
            $short_desc = $product->get_short_description();
            $is_in_stock = $product->is_in_stock();
            $permalink = $product->get_permalink();
            $brand = get_post_meta($product_id, '_brand', true);
            if (empty($brand)) {
                $terms = get_the_terms($product_id, 'product_cat');
                $brand = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'VELLAJOUS PERFUME';
            }
            if (empty($brand) || strtolower(trim($brand)) === 'uncategorized') {
                $brand = 'VELLAJOUS HAUTE PARFUMERIE';
            }

            // Gather all images (featured + gallery)
            $image_ids = [];
            if ($product->get_image_id()) {
                $image_ids[] = $product->get_image_id();
            }
            $gallery = $product->get_gallery_image_ids();
            if (!empty($gallery)) {
                foreach ($gallery as $gid) {
                    if (!in_array($gid, $image_ids)) {
                        $image_ids[] = $gid;
                    }
                }
            }

            ob_start();
            ?>
            <div class="vellajous-qv-modal" id="qv-modal-<?php echo esc_attr($product_id); ?>">
                <div class="vellajous-qv-grid">
                    <!-- Gallery Column -->
                    <div class="vellajous-qv-gallery">
                        <div class="vellajous-qv-gallery__main">
                            <a href="<?php echo esc_url($permalink); ?>" class="qv-img-link" title="<?php echo esc_attr($title); ?>">
                                <?php if (!empty($image_ids)) : ?>
                                    <img id="qv-active-img" src="<?php echo esc_url(wp_get_attachment_image_url($image_ids[0], 'large')); ?>" alt="<?php echo esc_attr($title); ?>" />
                                <?php else : ?>
                                    <img id="qv-active-img" src="<?php echo esc_url(wc_placeholder_img_src('large')); ?>" alt="<?php echo esc_attr($title); ?>" />
                                <?php endif; ?>
                            </a>
                            <?php if (!$is_in_stock) : ?>
                                <div class="vellajous-card__soldout-overlay">
                                    <span><?php esc_html_e('SOLD OUT', 'vellajous'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (count($image_ids) > 1) : ?>
                            <div class="vellajous-qv-gallery__thumbs">
                                <?php foreach ($image_ids as $idx => $img_id) : 
                                    $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                                    $full_url = wp_get_attachment_image_url($img_id, 'large');
                                ?>
                                    <button type="button" class="qv-thumb <?php echo $idx === 0 ? 'is-active' : ''; ?>" data-full="<?php echo esc_url($full_url); ?>">
                                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($title); ?>" />
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Details Column -->
                    <div class="vellajous-qv-content">
                        <div class="vellajous-qv-brand"><?php echo esc_html($brand); ?></div>
                        <h2 class="vellajous-qv-title"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h2>

                        <div class="vellajous-qv-price">
                            <?php echo $price_html; ?>
                        </div>

                        <!-- Luxury Assurance Pillars (Logo & Title Only) -->
                        <div class="vellajous-qv-assurance-pillars">
                            <div class="qv-assurance-item">
                                <div class="qv-assurance-icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/make-in-india-badge.png'); ?>" alt="<?php esc_attr_e('Made in India', 'vellajous'); ?>" width="28" height="28" />
                                </div>
                                <span class="qv-assurance-title"><?php esc_html_e('MADE IN INDIA', 'vellajous'); ?></span>
                            </div>
                            <div class="qv-assurance-item">
                                <div class="qv-assurance-icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/cruelty-free-badge.png'); ?>" alt="<?php esc_attr_e('CRUELTY-FREE', 'vellajous'); ?>" width="28" height="28" />
                                </div>
                                <span class="qv-assurance-title"><?php esc_html_e('CRUELTY-FREE', 'vellajous'); ?></span>
                            </div>
                            <div class="qv-assurance-item">
                                <div class="qv-assurance-icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/oil-concentration-badge.png'); ?>" alt="<?php esc_attr_e('RICH FRAGRANCE (30% OIL)', 'vellajous'); ?>" width="28" height="28" />
                                </div>
                                <span class="qv-assurance-title"><?php esc_html_e('RICH FRAGRANCE (30% OIL)', 'vellajous'); ?></span>
                            </div>
                            <div class="qv-assurance-item">
                                <div class="qv-assurance-icon">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/longevity-clock-badge.png'); ?>" alt="<?php esc_attr_e('MADE TO LAST (8Hr+ longevity)', 'vellajous'); ?>" width="28" height="28" />
                                </div>
                                <span class="qv-assurance-title"><?php esc_html_e('MADE TO LAST (8Hr+ longevity)', 'vellajous'); ?></span>
                            </div>
                        </div>

                        <?php if (!empty($short_desc)) : ?>
                            <div class="vellajous-qv-desc">
                                <?php echo wp_kses_post(wpautop($short_desc)); ?>
                            </div>
                        <?php else : ?>
                            <div class="vellajous-qv-desc">
                                <p><?php esc_html_e('Ethereal master formulation crafted with pure essences delivering 12+ hours of clean luxury sillage.', 'vellajous'); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="vellajous-qv-form">
                            <?php if ($is_in_stock) : ?>
                                <form class="vellajous-qv-cart-form" method="post" enctype="multipart/form-data">
                                    <div class="vellajous-quantity-row">
                                        <span class="qty-label"><?php esc_html_e('Quantity:', 'vellajous'); ?></span>
                                        <div class="vellajous-qty-input-wrap">
                                            <button type="button" class="qty-btn qty-minus">−</button>
                                            <input type="number" name="quantity" value="1" min="1" max="99" class="qty-field" />
                                            <button type="button" class="qty-btn qty-plus">+</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product_id); ?>" />
                                    <button type="submit" class="vellajous-btn vellajous-btn--primary vellajous-btn--block qv-add-btn">
                                        <span><?php esc_html_e('Add To Bag', 'vellajous'); ?></span>
                                        <span class="btn-loading"><span class="spinner"></span></span>
                                    </button>
                                </form>
                            <?php else : ?>
                                <div class="vellajous-soldout-box">
                                    <p class="soldout-msg"><?php esc_html_e('Currently Sold Out. Join the waitlist to be notified when available.', 'vellajous'); ?></p>
                                    <button type="button" class="vellajous-btn vellajous-btn--secondary vellajous-btn--block" disabled>
                                        <?php esc_html_e('Out of Stock', 'vellajous'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="vellajous-qv-footer-links">
                            <a href="<?php echo esc_url($permalink); ?>" class="qv-view-full">
                                <?php esc_html_e('View Full Product Details →', 'vellajous'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
            wp_send_json_success(['html' => $html]);
        }

        /**
         * Cart fragments for AJAX update
         */
        public function cart_fragments($fragments)
        {
            $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
            $fragments['span.vellajous-cart-count'] = '<span class="vellajous-cart-count">' . esc_html($count) . '</span>';
            
            ob_start();
            $this->render_drawer_cart_content();
            $fragments['div.vellajous-cart-drawer__body'] = ob_get_clean();

            return $fragments;
        }

        /**
         * Mini cart drawer content renderer (Ultra-Luxury Edition)
         */
        public function render_drawer_cart_content()
        {
            $cart_subtotal = (WC()->cart && method_exists(WC()->cart, 'get_subtotal')) ? floatval(WC()->cart->get_subtotal()) : 0;
            $free_shipping_threshold = 1999;
            $progress_percent = $free_shipping_threshold > 0 ? min(100, max(0, round(($cart_subtotal / $free_shipping_threshold) * 100))) : 100;
            $diff = $free_shipping_threshold - $cart_subtotal;
            ?>
            <div class="vellajous-cart-drawer__body">
                <!-- Free Shipping Progress Bar -->
                <div class="drawer-shipping-meter">
                    <div class="drawer-shipping-meter__text">
                        <?php if ($diff <= 0) : ?>
                            <span class="meter-unlocked">✨ <strong><?php esc_html_e('Congratulations!', 'vellajous'); ?></strong> <?php esc_html_e('You unlocked FREE Express Delivery!', 'vellajous'); ?></span>
                        <?php else : ?>
                            <span><?php printf(esc_html__('Add %s more to unlock FREE Express Delivery Across India!', 'vellajous'), '<strong>' . wc_price($diff) . '</strong>'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="drawer-shipping-meter__bar">
                        <div class="drawer-shipping-meter__fill" style="width: <?php echo esc_attr($progress_percent); ?>%;"></div>
                    </div>
                </div>

                <?php if (WC()->cart && !WC()->cart->is_empty()) : ?>
                    <ul class="vellajous-cart-items-list">
                        <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
                                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
                        ?>
                                <li class="vellajous-cart-item" data-product-id="<?php echo esc_attr($product_id); ?>">
                                    <div class="cart-item-img">
                                        <?php if ($product_permalink) : ?>
                                            <a href="<?php echo esc_url($product_permalink); ?>" class="vellajous-cart-product-link" data-product-id="<?php echo esc_attr($product_id); ?>" title="<?php esc_attr_e('View Product Details', 'vellajous'); ?>"><?php echo $thumbnail; ?></a>
                                        <?php else : ?>
                                            <?php echo $thumbnail; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="cart-item-info">
                                        <div class="cart-item-brand"><?php esc_html_e('VELLAJOUS DUBAI', 'vellajous'); ?></div>
                                        <h4 class="cart-item-title">
                                            <?php if ($product_permalink) : ?>
                                                <a href="<?php echo esc_url($product_permalink); ?>" class="vellajous-cart-product-link" data-product-id="<?php echo esc_attr($product_id); ?>" title="<?php esc_attr_e('View Product Details', 'vellajous'); ?>"><?php echo esc_html($_product->get_name()); ?></a>
                                            <?php else : ?>
                                                <?php echo esc_html($_product->get_name()); ?>
                                            <?php endif; ?>
                                        </h4>
                                        <div class="cart-item-meta">
                                            <span class="qty"><?php echo esc_html($cart_item['quantity']); ?> ×</span>
                                            <span class="price"><?php echo WC()->cart->get_product_price($_product); ?></span>
                                        </div>
                                        <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>" 
                                           class="cart-item-remove remove_from_cart_button" 
                                           data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>"
                                           aria-label="<?php esc_attr_e('Remove this item', 'vellajous'); ?>">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            <span><?php esc_html_e('Remove', 'vellajous'); ?></span>
                                        </a>
                                    </div>
                                </li>
                        <?php endif; endforeach; ?>
                    </ul>
                    <div class="vellajous-cart-drawer__footer">
                        <div class="drawer-subtotal">
                            <span class="subtotal-label"><?php esc_html_e('Subtotal', 'vellajous'); ?></span>
                            <span class="subtotal-amount"><?php wc_cart_totals_subtotal_html(); ?></span>
                        </div>
                        <p class="drawer-shipping-note">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            <span><?php esc_html_e('Taxes included · Fast Express Shipping Across India', 'vellajous'); ?></span>
                        </p>
                        <div class="drawer-actions">
                            <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="vellajous-btn vellajous-btn--primary vellajous-btn--block drawer-checkout-btn">
                                <span><?php esc_html_e('PROCEED TO CHECKOUT', 'vellajous'); ?></span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </a>
                            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="vellajous-btn vellajous-btn--outline vellajous-btn--block drawer-view-bag-btn">
                                <?php esc_html_e('View Full Shopping Bag', 'vellajous'); ?>
                            </a>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="vellajous-cart-empty">
                        <div class="cart-empty-icon">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                        </div>
                        <h3><?php esc_html_e('Your Bag is Empty', 'vellajous'); ?></h3>
                        <p><?php esc_html_e('Discover our exquisite luxury perfumes crafted in Dubai.', 'vellajous'); ?></p>
                        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="vellajous-btn vellajous-btn--primary">
                            <?php esc_html_e('Explore Best Sellers →', 'vellajous'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }

        public function ajax_get_mini_cart()
        {
            ob_start();
            $this->render_drawer_cart_content();
            $html = ob_get_clean();
            wp_send_json_success(['html' => $html]);
        }

        /**
         * Set exactly 8 products per page on shop and category archives
         */
        public function set_products_per_page($query)
        {
            if (!is_admin() && $query->is_main_query()) {
                if (is_shop() || is_product_taxonomy()) {
                    $query->set('posts_per_page', 8);
                }

                if (is_shop() || $query->is_post_type_archive('product')) {
                    $slug_map = [
                        'healthcare' => 'healthcare-public-service',
                        'business'   => 'business-professional',
                        'design'     => 'design-build',
                        'travel'     => 'travel-adventure',
                        'calm'       => 'calm-wind-down',
                        'focus'      => 'focus-reset',
                        'energy'     => 'energy-confidence',
                        'bold'       => 'bold-magnetic',
                    ];

                    $target_slug = '';
                    if (!empty($_GET['persona'])) {
                        $p = sanitize_title(wp_unslash($_GET['persona']));
                        $target_slug = $slug_map[$p] ?? $p;
                    } elseif (!empty($_GET['mood'])) {
                        $m = sanitize_title(wp_unslash($_GET['mood']));
                        $target_slug = $slug_map[$m] ?? $m;
                    } elseif (!empty($_GET['category'])) {
                        $c = sanitize_title(wp_unslash($_GET['category']));
                        $target_slug = $slug_map[$c] ?? $c;
                    } elseif (!empty($_GET['product_cat'])) {
                        $target_slug = sanitize_title(wp_unslash($_GET['product_cat']));
                    }

                    if (!empty($target_slug)) {
                        $tax_query = (array) $query->get('tax_query');
                        $tax_query[] = [
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => $target_slug,
                            'operator' => 'IN',
                        ];
                        $query->set('tax_query', $tax_query);
                    }
                }
            }
        }

        /**
         * AJAX handler for Loading More Products (8 per batch)
         */
        public function ajax_load_more_products()
        {
            $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 2;
            $category_id = isset($_POST['category']) ? absint($_POST['category']) : 0;
            $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'menu_order';

            $args = [
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => 8,
                'paged'          => $paged,
            ];

            if ($category_id > 0) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $category_id,
                    ],
                ];
            }

            switch ($orderby) {
                case 'price':
                    $args['meta_key'] = '_price';
                    $args['orderby']  = 'meta_value_num';
                    $args['order']    = 'ASC';
                    break;
                case 'price-desc':
                    $args['meta_key'] = '_price';
                    $args['orderby']  = 'meta_value_num';
                    $args['order']    = 'DESC';
                    break;
                case 'date':
                    $args['orderby'] = 'date';
                    $args['order']   = 'DESC';
                    break;
                case 'popularity':
                    $args['meta_key'] = 'total_sales';
                    $args['orderby']  = 'meta_value_num';
                    $args['order']    = 'DESC';
                    break;
                case 'rating':
                    $args['meta_key'] = '_wc_average_rating';
                    $args['orderby']  = 'meta_value_num';
                    $args['order']    = 'DESC';
                    break;
                default:
                    $args['orderby'] = 'menu_order title';
                    $args['order']   = 'ASC';
                    break;
            }

            $loop = new WP_Query($args);
            ob_start();
            if ($loop->have_posts()) {
                while ($loop->have_posts()) {
                    $loop->the_post();
                    echo self::render_product_card(get_the_ID());
                }
            }
            $html = ob_get_clean();
            wp_reset_postdata();

            $max_pages = $loop->max_num_pages;
            $has_more  = ($paged < $max_pages);

            wp_send_json_success([
                'html'        => $html,
                'has_more'    => $has_more,
                'paged'       => $paged,
                'max_pages'   => $max_pages,
                'found_posts' => $loop->found_posts,
            ]);
        }

        /**
         * Process Luxury Contact Form via AJAX
         */
        public function ajax_submit_contact()
        {
            check_ajax_referer('vellajous_contact_nonce', 'security');

            $name    = sanitize_text_field($_POST['contact_name'] ?? '');
            $email   = sanitize_email($_POST['contact_email'] ?? '');
            $phone   = sanitize_text_field($_POST['contact_phone'] ?? '');
            $reason  = sanitize_text_field($_POST['contact_reason'] ?? 'General Inquiry');
            $subject = sanitize_text_field($_POST['contact_subject'] ?? 'New Concierge Inquiry');
            $message = sanitize_textarea_field($_POST['contact_message'] ?? '');

            if (empty($name) || empty($email) || empty($message)) {
                wp_send_json_error([
                    'message' => __('Please ensure your name, email, and message are provided.', 'vellajous')
                ]);
            }

            if (!is_email($email)) {
                wp_send_json_error([
                    'message' => __('Please enter a valid email address.', 'vellajous')
                ]);
            }

            $to      = get_option('admin_email');
            $headers = [
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . esc_attr($name) . ' <' . esc_attr($email) . '>',
                'Reply-To: ' . esc_attr($email),
            ];
            $body = "<h2>Vellajous Privé Concierge Inquiry</h2>";
            $body .= "<p><strong>Client Name:</strong> " . esc_html($name) . "</p>";
            $body .= "<p><strong>Email:</strong> " . esc_html($email) . "</p>";
            $body .= "<p><strong>Phone:</strong> " . esc_html($phone) . "</p>";
            $body .= "<p><strong>Inquiry Type:</strong> " . esc_html($reason) . "</p>";
            $body .= "<p><strong>Subject:</strong> " . esc_html($subject) . "</p>";
            $body .= "<hr><p><strong>Message:</strong><br>" . nl2br(esc_html($message)) . "</p>";

            wp_mail($to, 'Vellajous Concierge: ' . $subject, $body, $headers);

            wp_send_json_success([
                'message' => __('Thank you for contacting Vellajous Privé. Your inquiry has been received. Our olfactory concierge will respond to your email within 4 business hours.', 'vellajous')
            ]);
        }

        /**
         * Fetch Wishlist Products HTML via AJAX
         */
        public function ajax_get_wishlist_items()
        {
            $raw_ids = $_POST['ids'] ?? [];
            if (!is_array($raw_ids)) {
                $raw_ids = array_filter(array_map('trim', explode(',', (string)$raw_ids)));
            }
            $clean_ids = array_filter(array_map('absint', $raw_ids));

            if (empty($clean_ids)) {
                wp_send_json_success([
                    'html'  => '',
                    'count' => 0,
                    'empty' => true
                ]);
            }

            $query = new WP_Query([
                'post_type'      => 'product',
                'post__in'       => $clean_ids,
                'posts_per_page' => -1,
                'orderby'        => 'post__in'
            ]);

            ob_start();
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    echo self::render_product_card(get_the_ID());
                }
            }
            $html = ob_get_clean();
            wp_reset_postdata();

            wp_send_json_success([
                'html'  => $html,
                'count' => count($clean_ids),
                'empty' => false
            ]);
        }

        /**
         * Handle Order PDF Download & Printable Invoice Requests
         */
        public function handle_order_pdf_request()
        {
            if (!isset($_GET['vellajous_action'])) {
                return;
            }

            $action = sanitize_text_field($_GET['vellajous_action']);
            if (!in_array($action, ['download_order_pdf', 'view_order_invoice'], true)) {
                return;
            }

            $order_id  = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
            $order_key = isset($_GET['order_key']) ? sanitize_text_field($_GET['order_key']) : '';

            if (!$order_id) {
                wp_die(esc_html__('Missing order identification.', 'vellajous'), esc_html__('Access Denied', 'vellajous'), ['response' => 400]);
            }

            $order = wc_get_order($order_id);
            if (!$order) {
                wp_die(esc_html__('Order not found.', 'vellajous'), esc_html__('Not Found', 'vellajous'), ['response' => 404]);
            }

            // Security verification: order key or logged in customer or shop manager/admin
            $authorized = false;
            if (!empty($order_key) && hash_equals($order->get_order_key(), $order_key)) {
                $authorized = true;
            } elseif (is_user_logged_in() && (get_current_user_id() === (int) $order->get_user_id() || current_user_can('manage_woocommerce'))) {
                $authorized = true;
            }

            if (!$authorized) {
                wp_die(esc_html__('You do not have permission to view or download this order consignment manifest.', 'vellajous'), esc_html__('Access Denied', 'vellajous'), ['response' => 403]);
            }

            require_once vellajous_DIR . '/inc/classes/class_vellajous_order_pdf.php';

            if ($action === 'download_order_pdf') {
                class_vellajous_order_pdf::generate_and_download($order);
                exit;
            }

            if ($action === 'view_order_invoice') {
                class_vellajous_order_pdf::render_html_invoice($order);
                exit;
            }
        }
    }
}
