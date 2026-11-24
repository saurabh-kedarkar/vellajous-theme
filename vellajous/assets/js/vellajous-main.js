/**
 * Vellajous Main JavaScript
 * Handles video hero sound toggle, sticky header, quick view modal, 
 * mini cart drawer, and AJAX interactions.
 */

(function ($) {
    'use strict';

    $(document).ready(function () {
        initStickyHeader();
        initHeroVideo();
        initQuickView();
        initCartDrawer();
        initMobileMenu();
        initInteractiveReviews();
        initWeartestSlider();
        initScrollReveal();
        initAjaxLoadMore();
        initProductGallery();
        initProductAccordions();
        initProductAddToCart();
        initProductBuyNow();
        initProductShare();
        initProductTabs();
        initStickyAddToCartBar();
        initContactPage();
        initMyAccountTabs();
        initInstagramCarousel();
        initCustomCursor();
        initBottomNav();
        initScentQuiz();
        initNavigationDropdowns();
        initWishlist();
        initCheckoutEnhancements();
    });

    /**
     * 1. Sticky Header & Smooth Anchor Scrolling
     */
    function initStickyHeader() {
        var $header = $('.site-header');
        if (!$header.length) return;

        var scrollTicking = false;

        function checkScroll() {
            var scrollPos = window.pageYOffset || document.documentElement.scrollTop || 0;

            if (scrollPos > 30) {
                if (!$header.hasClass('is-scrolled')) {
                    $header.addClass('is-scrolled');
                }
            } else {
                if ($header.hasClass('is-scrolled')) {
                    $header.removeClass('is-scrolled');
                }
            }
            scrollTicking = false;
        }

        // 60fps/120fps passive scroll listener - zero thread lag or jank
        window.addEventListener('scroll', function () {
            if (!scrollTicking) {
                window.requestAnimationFrame(checkScroll);
                scrollTicking = true;
            }
        }, { passive: true });

        checkScroll();

        // Native smooth scroll for anchor links without jQuery animation stutter
        $(document).on('click', 'a[href*="#"]', function (e) {
            var href = $(this).attr('href');
            if (!href || href === '#' || href.indexOf('#') === -1) return;

            var hash = href.substring(href.indexOf('#'));
            if (hash && hash !== '#' && $(hash).length) {
                e.preventDefault();
                var headerHeight = $('.site-header').outerHeight() || 64;
                var targetTop = $(hash).offset().top - headerHeight + 5;
                if (targetTop < 0) targetTop = 0;

                try {
                    window.scrollTo({
                        top: targetTop,
                        behavior: 'smooth'
                    });
                } catch (err) {
                    $('html, body').stop().animate({ scrollTop: targetTop }, 500);
                }

                if (window.history && window.history.pushState) {
                    window.history.pushState(null, null, hash);
                }
            }
        });
    }

    /**
     * 2. Hero Master Banner (Dual-Mode: High-Res Slider & HD Video Switcher)
     */
    function initHeroVideo() {
        var $master = $('#heroMasterBanner');
        var video = document.getElementById('heroVideo');
        var $soundBtn = $('#heroSoundToggle');
        var $switcherBtns = $('.hero-switcher-btn');
        var $sliderLayer = $('#heroSliderLayer');
        var $videoLayer = $('#heroVideoLayer');
        var $slides = $('.hero-slide');
        var $dots = $('.hero-slider-dot');

        // Sound Toggle
        if (video && $soundBtn.length) {
            $soundBtn.on('click', function () {
                if (video.muted) {
                    video.muted = false;
                    $soundBtn.html('<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>');
                    $soundBtn.attr('title', 'Mute Sound');
                } else {
                    video.muted = true;
                    $soundBtn.html('<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line></svg>');
                    $soundBtn.attr('title', 'Unmute Sound');
                }
            });
        }

        if (!$master.length) return;

        // Slider Logic
        var currentSlide = 0;
        var totalSlides = $slides.length;
        var slideTimer = null;
        var isHovered = false;

        function goToSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentSlide = index;

            $slides.removeClass('is-active').eq(currentSlide).addClass('is-active');
            $dots.removeClass('is-active').eq(currentSlide).addClass('is-active');
        }

        function nextSlide() {
            goToSlide(currentSlide + 1);
        }

        function prevSlide() {
            goToSlide(currentSlide - 1);
        }

        function startSlideShow() {
            stopSlideShow();
            if ($sliderLayer.hasClass('is-active') && totalSlides > 1) {
                slideTimer = setInterval(function () {
                    if (!isHovered) {
                        nextSlide();
                    }
                }, 5200);
            }
        }

        function stopSlideShow() {
            if (slideTimer) {
                clearInterval(slideTimer);
                slideTimer = null;
            }
        }

        $('#heroSlidePrev').on('click', function (e) {
            e.preventDefault();
            prevSlide();
            startSlideShow();
        });

        $('#heroSlideNext').on('click', function (e) {
            e.preventDefault();
            nextSlide();
            startSlideShow();
        });

        $dots.on('click', function (e) {
            e.preventDefault();
            var idx = parseInt($(this).data('slide-to'), 10);
            goToSlide(idx);
            startSlideShow();
        });

        $sliderLayer.on('mouseenter', function () {
            isHovered = true;
        }).on('mouseleave', function () {
            isHovered = false;
        });

        // Touch Swipe for Mobile (Ultra-responsive native finger gesture)
        var touchStartX = 0;
        var touchStartY = 0;
        var touchStartTime = 0;
        var touchMoved = false;

        var bannerEl = document.getElementById('heroMasterBanner') || document.getElementById('heroSliderLayer');
        if (bannerEl) {
            bannerEl.addEventListener('touchstart', function (e) {
                if (!e.touches || e.touches.length > 1) return;
                var t = e.touches[0];
                touchStartX = t.clientX;
                touchStartY = t.clientY;
                touchStartTime = Date.now();
                touchMoved = false;
            }, { passive: true });

            bannerEl.addEventListener('touchmove', function (e) {
                if (!e.touches || e.touches.length > 1) return;
                touchMoved = true;
            }, { passive: true });

            bannerEl.addEventListener('touchend', function (e) {
                if (!e.changedTouches || e.changedTouches.length === 0) return;
                var t = e.changedTouches[0];
                var diffX = touchStartX - t.clientX;
                var diffY = touchStartY - t.clientY;
                var elapsed = Date.now() - touchStartTime;

                // Threshold: 30px swipe, or 20px flick if fast (<350ms)
                var isFlick = elapsed < 350 && Math.abs(diffX) > 20;
                var isSwipe = Math.abs(diffX) > 30;

                if ((isSwipe || isFlick) && Math.abs(diffX) > Math.abs(diffY) * 1.1) {
                    if (diffX > 0) {
                        nextSlide(); // Finger swiped left -> next slide
                    } else {
                        prevSlide(); // Finger swiped right -> previous slide
                    }
                    startSlideShow();
                }
            }, { passive: true });

            bannerEl.addEventListener('touchcancel', function () {
                touchMoved = false;
            }, { passive: true });
        }

        // Switcher Logic (Photos vs Video)
        function setBannerMode(targetType, save) {
            $master.attr('data-active-type', targetType);
            $switcherBtns.removeClass('is-active').filter('[data-media-target="' + targetType + '"]').addClass('is-active');

            if (targetType === 'slider') {
                $sliderLayer.addClass('is-active');
                $videoLayer.removeClass('is-active');
                if (video) {
                    try { video.pause(); } catch(e) {}
                }
                if ($soundBtn.length) $soundBtn.hide();
                startSlideShow();
            } else {
                $videoLayer.addClass('is-active');
                $sliderLayer.removeClass('is-active');
                stopSlideShow();
                if (video) {
                    try {
                        var p = video.play();
                        if (p !== undefined) {
                            p.catch(function () {
                                video.muted = true;
                                video.play();
                            });
                        }
                    } catch(e) {}
                }
                if ($soundBtn.length) $soundBtn.show();
            }

            if (save !== false) {
                try { localStorage.setItem('vellajous_hero_type', targetType); } catch(e) {}
            }
        }

        // Initialize active banner type configured in code
        var initial = $master.attr('data-active-type') || 'slider';
        setBannerMode(initial, false);
    }

    /**
     * 3. Ultra-Fast Quick View Modal (Instant Pre-fetch & Memory Cache)
     */
    function initQuickView() {
        var $backdrop = $('#vellajousQuickViewModal');
        var $container = $backdrop.find('.vellajous-modal-container');
        var qvCache = {}; // In-Memory Cache (0ms instant display)
        var currentLoadingId = null;

        function renderQuickViewHtml(html) {
            $container.html('<button type="button" class="vellajous-modal-close" aria-label="Close modal">&times;</button>' + html);
            $backdrop.addClass('is-open');
            $('body').css('overflow', 'hidden');
        }

        function showLoadingSkeleton() {
            $container.html(
                '<button type="button" class="vellajous-modal-close" aria-label="Close modal">&times;</button>' +
                '<div class="vellajous-qv-skeleton">' +
                    '<div class="qv-skeleton-grid">' +
                        '<div class="qv-skeleton-img"></div>' +
                        '<div class="qv-skeleton-text">' +
                            '<div class="qv-skeleton-line short"></div>' +
                            '<div class="qv-skeleton-line title"></div>' +
                            '<div class="qv-skeleton-line stars"></div>' +
                            '<div class="qv-skeleton-line price"></div>' +
                            '<div class="qv-skeleton-line tags"></div>' +
                            '<div class="qv-skeleton-line desc"></div>' +
                            '<div class="qv-skeleton-line btn"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            $backdrop.addClass('is-open');
            $('body').css('overflow', 'hidden');
        }

        function openQuickView(productId, $trigger) {
            if (!productId) return;

            // 1. Instant Cache Hit (0ms)
            if (qvCache[productId]) {
                renderQuickViewHtml(qvCache[productId]);
                return;
            }

            // 2. Immediate Visual Response (<10ms)
            currentLoadingId = productId;
            if ($trigger && $trigger.length) {
                $trigger.css('opacity', '0.7');
            }
            showLoadingSkeleton();

            // 3. Fetch via AJAX
            $.ajax({
                url: vellajous_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'vellajous_quick_view',
                    nonce: vellajous_data.nonce,
                    product_id: productId
                },
                success: function (res) {
                    if ($trigger && $trigger.length) {
                        $trigger.css('opacity', '');
                    }
                    if (res.success && res.data && res.data.html) {
                        qvCache[productId] = res.data.html;
                        // Render only if still looking at this product
                        if ($backdrop.hasClass('is-open') && currentLoadingId === productId) {
                            renderQuickViewHtml(res.data.html);
                        }
                    }
                },
                error: function () {
                    if ($trigger && $trigger.length) {
                        $trigger.css('opacity', '');
                    }
                }
            });
        }

        // Prefetch on hover for 0ms instant display
        function prefetchQuickView(productId) {
            if (!productId || qvCache[productId]) return;
            $.ajax({
                url: vellajous_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'vellajous_quick_view',
                    nonce: vellajous_data.nonce,
                    product_id: productId
                },
                success: function (res) {
                    if (res.success && res.data && res.data.html) {
                        qvCache[productId] = res.data.html;
                    }
                }
            });
        }

        // Trigger 1: Product card quick view button (only when clicking the dedicated quickview icon)
        $(document).on('click', '.vellajous-quickview-btn', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var productId = $(this).data('product-id');
            openQuickView(productId, $(this));
        });

        // Trigger 1b: Clicking on the product card navigates directly to inner product page
        $(document).on('click', '.vellajous-card', function (e) {
            // Exclude action buttons: Quick Add, Dedicated Quick View icon, Wishlist
            if ($(e.target).closest('.vellajous-btn--quick-add, .vellajous-quickview-btn, .vellajous-wishlist-btn, .add_to_cart_button').length) {
                return;
            }
            // If already clicking an anchor link inside card, let standard navigation proceed
            if ($(e.target).closest('a').length) {
                return;
            }
            // Otherwise find permalink from card title or image link and redirect
            var $link = $(this).find('.vellajous-card__title a, .vellajous-card__image-link').first();
            var href = $link.attr('href');
            if (href && href !== '#' && href !== 'javascript:void(0);') {
                window.location.href = href;
            }
        });

        // Trigger 2: Cart drawer items (Image or Title click) redirect directly to the inner product page
        $(document).on('click', '.vellajous-cart-item .cart-item-img a, .vellajous-cart-item .cart-item-title a, .vellajous-cart-product-link', function (e) {
            var href = $(this).attr('href');
            if (href && href !== '#' && href !== 'javascript:void(0);') {
                $('#vellajousCartDrawerBackdrop').removeClass('is-open');
                $('body').removeClass('drawer-open');
                window.location.href = href;
            }
        });

        // Intelligent prefetch on mouse hover for dedicated quickview icon only
        $(document).on('mouseenter', '.vellajous-quickview-btn', function () {
            var productId = $(this).data('product-id');
            if (productId) {
                prefetchQuickView(productId);
            }
        });

        // Close modal
        $(document).on('click', '.vellajous-modal-close, .vellajous-modal-backdrop', function (e) {
            if (e.target === this || $(this).hasClass('vellajous-modal-close')) {
                $backdrop.removeClass('is-open');
                $('body').css('overflow', '');
            }
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $backdrop.hasClass('is-open')) {
                $backdrop.removeClass('is-open');
                $('body').css('overflow', '');
            }
        });

        // Click "View Full Product Details →"
        $(document).on('click', '.qv-view-full', function () {
            $backdrop.removeClass('is-open');
            $('body').css('overflow', '');
        });

        // Gallery thumbnail switcher in Quick View
        $(document).on('click', '.qv-thumb', function () {
            var fullUrl = $(this).data('full');
            if (fullUrl) {
                $('#qv-active-img').attr('src', fullUrl);
                $('.qv-thumb').removeClass('is-active');
                $(this).addClass('is-active');
            }
        });

        // Quantity controls
        $(document).on('click', '.qty-btn', function () {
            var $input = $(this).siblings('.qty-field');
            var val = parseInt($input.val(), 10) || 1;
            if ($(this).hasClass('qty-plus')) {
                $input.val(val + 1);
            } else if ($(this).hasClass('qty-minus') && val > 1) {
                $input.val(val - 1);
            }
        });

        // Handle Quick View form submission via AJAX
        $(document).on('submit', '.vellajous-qv-cart-form', function (e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('.qv-add-btn');
            var productId = $form.find('input[name="add-to-cart"]').val();
            var quantity = $form.find('input[name="quantity"]').val() || 1;
            $btn.addClass('is-loading');

            // Use WooCommerce's built-in AJAX add to cart (wc-ajax=add_to_cart)
            var wcAjaxUrl = vellajous_data.wc_ajax_url || (window.location.origin + '/?wc-ajax=add_to_cart');

            $.ajax({
                url: wcAjaxUrl,
                type: 'POST',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                success: function (res) {
                    $btn.removeClass('is-loading');
                    $backdrop.removeClass('is-open');
                    $('body').css('overflow', '');
                    if (res && res.fragments) {
                        // Update WooCommerce fragments (cart count, etc.)
                        $.each(res.fragments, function (key, value) {
                            $(key).replaceWith(value);
                        });
                    }
                    $(document.body).trigger('wc_fragment_refresh');
                    setTimeout(function () { openCartDrawer(); }, 200);
                },
                error: function () {
                    // Fallback: POST to current page (standard WC add to cart)
                    $btn.removeClass('is-loading');
                    $.ajax({
                        url: window.location.href,
                        type: 'POST',
                        data: $form.serialize(),
                        success: function () {
                            $backdrop.removeClass('is-open');
                            $('body').css('overflow', '');
                            $(document.body).trigger('wc_fragment_refresh');
                            setTimeout(function () { openCartDrawer(); }, 300);
                        }
                    });
                }
            });
        });
    }

    /**
     * 4. Slide-in Mini Cart Drawer
     */
    function initCartDrawer() {
        var $drawerBackdrop = $('#vellajousCartDrawerBackdrop');

        $('.header-cart-btn').on('click', function (e) {
            e.preventDefault();
            openCartDrawer();
        });

        $(document).on('click', '.vellajous-drawer-close, #vellajousCartDrawerBackdrop', function (e) {
            if (e.target === this || $(this).hasClass('vellajous-drawer-close')) {
                closeCartDrawer();
            }
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $drawerBackdrop.hasClass('is-open')) {
                closeCartDrawer();
            }
        });

        // Show loading spinner on Quick Add button while WooCommerce adds item
        $(document.body).on('adding_to_cart', function (e, $btn) {
            if ($btn && $btn.hasClass('vellajous-btn--quick-add')) {
                $btn.addClass('is-loading');
            }
        });

        // Open cart drawer on WooCommerce added_to_cart event
        $(document.body).on('added_to_cart', function (e, fragments, cart_hash, $btn) {
            if ($btn && $btn.hasClass('vellajous-btn--quick-add')) {
                $btn.removeClass('is-loading');
            }
            openCartDrawer();
        });

        // Handle remove from cart in drawer via AJAX
        $(document).on('click', '.cart-item-remove', function (e) {
            e.preventDefault();
            var $item = $(this).closest('.vellajous-cart-item');
            var removeUrl = $(this).attr('href');
            $item.css('opacity', '0.35');

            $.get(removeUrl, function () {
                $(document.body).trigger('wc_fragment_refresh');
                openCartDrawer();
            });
        });
    }

    function openCartDrawer() {
        var $drawerBackdrop = $('#vellajousCartDrawerBackdrop');
        $drawerBackdrop.addClass('is-open');
        $('body').css('overflow', 'hidden');

        // Refresh mini cart content
        $.ajax({
            url: vellajous_data.ajax_url,
            type: 'POST',
            data: {
                action: 'vellajous_get_mini_cart',
                nonce: vellajous_data.nonce
            },
            success: function (res) {
                if (res.success && res.data && res.data.html) {
                    $drawerBackdrop.find('.vellajous-cart-drawer__body').replaceWith(res.data.html);
                }
            }
        });
    }

    function closeCartDrawer() {
        var $drawerBackdrop = $('#vellajousCartDrawerBackdrop');
        $drawerBackdrop.removeClass('is-open');
        $('body').css('overflow', '');
    }

    /**
     * 5. Mobile Menu Toggle
     */
    function initMobileMenu() {
        var $btn = $('#mobileNavToggle');
        var $menu = $('#mobileNavDrawer');
        var $overlay = $('#mobileNavOverlay');
        if (!$btn.length || !$menu.length) return;

        function openMenu() {
            $menu.addClass('is-open').attr('aria-hidden', 'false');
            $overlay.addClass('is-open');
            $('body').addClass('nav-open');
        }

        function closeMenu() {
            $menu.removeClass('is-open').attr('aria-hidden', 'true');
            $overlay.removeClass('is-open');
            $('body').removeClass('nav-open');
        }

        $btn.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if ($menu.hasClass('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        $(document).on('click', '.mobile-nav-close, #mobileNavOverlay', function (e) {
            e.preventDefault();
            closeMenu();
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $menu.hasClass('is-open')) {
                closeMenu();
            }
        });

        $menu.find('a').on('click', function (e) {
            // Don't close drawer for parent menu items with submenus (href='#')
            var $link = $(this);
            if ($link.attr('href') === '#' || $link.hasClass('mobile-main-link')) {
                e.preventDefault();
                return;
            }
            closeMenu();
        });
    }

    /**
     * 6. Interactive Customer Avatar Carousel (Screenshot 1)
     * Auto-sliding with focus locked on center slot (slot-2)
     * Zero layout shift/jitter during slide transitions.
     */
    function initInteractiveReviews() {
        var reviewsData = [
            {
                stars: '★★★★★',
                quote: 'Ordered ASMIRA for my anniversary. The packaging is pure luxury and the floral aroma lasts through the entire day. Absolutely exquisite luxury perfume.',
                author: '— Aarav M.',
                meta: 'Verified buyer · ASMIRA Aqua Parfum · Mumbai',
                avatarFile: 'avatar1.jpg'
            },
            {
                stars: '★★★★★',
                quote: 'The sillage is incredible. Unlike chemical alcohol perfumes that evaporate quickly, this subtle oil essence warms with your skin. 10/10 recommend!',
                author: '— Neha D.',
                meta: 'Verified buyer · Sublime Oud · New Delhi',
                avatarFile: 'avatar2.jpg'
            },
            {
                stars: '★★★★☆',
                quote: 'Delivered in three days. The cold marine opening stays crisp right through a working day.',
                author: '— Faizan A.',
                meta: 'Verified buyer · Aqua Rush · Hyderabad',
                avatarFile: 'avatar3.jpg'
            },
            {
                stars: '★★★★★',
                quote: 'Eclipse is mesmerizing! The smoky amber and orange notes make heads turn every time. Safe on my sensitive skin with zero irritation.',
                author: '— Priya S.',
                meta: 'Verified buyer · Eclipse · Pune',
                avatarFile: 'avatar4.jpg'
            },
            {
                stars: '★★★★★',
                quote: 'Lamsa Gift Set is the best gift I have given to myself. Premium presentation and long-lasting aroma. Fast delivery in just 2 days.',
                author: '— Sneha K.',
                meta: 'Verified buyer · Lamsa Gift Set · Bengaluru',
                avatarFile: 'avatar5.jpg'
            }
        ];

        var $testimonials = $('#testimonials');
        var customData = $testimonials.attr('data-reviews');
        if (customData) {
            try {
                var parsed = JSON.parse(customData);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    reviewsData = parsed;
                }
            } catch (e) {}
        }

        var total = reviewsData.length;
        var currentIndex = total >= 3 ? 2 : 0; // Default Faizan A. (Center in screenshot)
        var $track = $('#avatarTrack');
        var $dots = $('#reviewDots .dot');
        var $inner = $('#reviewDisplayInner');
        var autoSlideTimer = null;

        // Base upload URL for avatars from slot-2 existing img
        var initialImg = $('.avatar-bubble.slot-2 img').attr('src') || '';
        var avatarBaseUrl = initialImg.substring(0, initialImg.lastIndexOf('/') + 1);

        function renderActive(index) {
            if (index < 0) {
                index = (index % total + total) % total;
            } else {
                index = index % total;
            }
            currentIndex = index;

            // 1. Smoothly switch foreach-rendered review cards
            var $cards = $('#reviewDisplayWrap .review-display-card');
            if ($cards.length) {
                $cards.each(function () {
                    var cardIdx = parseInt($(this).data('index'), 10);
                    if (cardIdx === currentIndex) {
                        $(this).addClass('is-active').css('display', 'flex').hide().fadeIn(250);
                    } else {
                        $(this).removeClass('is-active').css('display', 'none');
                    }
                });
            }

            // Fallback: update text inside legacy single #reviewDisplayInner if present
            if ($inner.length) {
                $inner.addClass('is-updating');
                setTimeout(function () {
                    var data = reviewsData[currentIndex];
                    if (data) {
                        var desc = data.description || data.quote || '';
                        var revMeta = data.reviews || data.meta || '';
                        $('#reviewStars').text(data.stars || '★★★★★');
                        $('#reviewQuote').text('“' + desc.replace(/[“”"]/g, '') + '”');
                        $('#reviewAuthor').text(data.author || ('— ' + (data.name || '')));
                        $('#reviewMetaText').text(revMeta);
                    }
                    $inner.removeClass('is-updating');
                }, 160);
            }

            // 2. Update pagination pill dots
            $dots.removeClass('is-active');
            $dots.filter('[data-index="' + currentIndex + '"]').addClass('is-active');

            // 3. Ultra-smooth 60fps avatar repositioning
            var $bubbles = $track.find('.avatar-bubble');
            $bubbles.each(function () {
                var itemIdx = $(this).data('index');
                if (typeof itemIdx === 'undefined') {
                    itemIdx = $(this).data('slot');
                }
                itemIdx = parseInt(itemIdx, 10);

                var diff = (itemIdx - currentIndex) % total;
                if (diff > 2) diff -= total;
                if (diff < -2) diff += total;

                // Remove previous slot/pos classes
                $(this).removeClass('pos-center pos-left-1 pos-right-1 pos-left-2 pos-right-2 pos-hidden slot-0 slot-1 slot-2 slot-3 slot-4 is-active');

                if (diff === 0) {
                    $(this).addClass('pos-center is-active');
                } else if (diff === -1) {
                    $(this).addClass('pos-left-1');
                } else if (diff === 1) {
                    $(this).addClass('pos-right-1');
                } else if (diff === -2) {
                    $(this).addClass('pos-left-2');
                } else if (diff === 2) {
                    $(this).addClass('pos-right-2');
                } else {
                    $(this).addClass('pos-hidden');
                }
            });
        }

        // Auto-slider controls
        function startAutoSlider() {
            stopAutoSlider();
            autoSlideTimer = setInterval(function () {
                renderActive(currentIndex + 1);
            }, 4000);
        }

        function stopAutoSlider() {
            if (autoSlideTimer) {
                clearInterval(autoSlideTimer);
                autoSlideTimer = null;
            }
        }

        // Click on any avatar bubble to center it
        $track.on('click', '.avatar-bubble', function () {
            var idx = $(this).data('index');
            if (typeof idx === 'undefined') {
                var slot = parseInt($(this).data('slot'), 10);
                var diff = slot - 2;
                idx = (currentIndex + diff + total) % total;
            } else {
                idx = parseInt(idx, 10);
            }
            renderActive(idx);
            startAutoSlider();
        });

        // Navigation arrows
        $('#avatarPrevBtn').on('click', function () {
            renderActive(currentIndex - 1);
            startAutoSlider();
        });

        $('#avatarNextBtn').on('click', function () {
            renderActive(currentIndex + 1);
            startAutoSlider();
        });

        // Click on pagination dots
        $dots.on('click', function () {
            var idx = parseInt($(this).data('index'), 10);
            renderActive(idx);
            startAutoSlider();
        });

        // Pause auto-sliding on user hover
        $('#testimonials, #avatarTrack').on('mouseenter', stopAutoSlider).on('mouseleave', startAutoSlider);

        // Initial render
        renderActive(currentIndex);
        startAutoSlider();
    }

    /**
     * 7. Scroll Reveal Animations
     * Elegantly reveals cards, headers, and grids as the user scrolls
     */
    function initScrollReveal() {
        // Automatically add reveal classes and stagger delays
        $('.trust-bar__grid .trust-item').each(function (i) {
            $(this).addClass('scroll-reveal stagger-' + ((i % 4) + 1));
        });

        $('.attitude-grid .attitude-card').each(function (i) {
            $(this).addClass('scroll-reveal stagger-' + ((i % 6) + 1));
        });

        $('.weartest-grid .weartest-card, .weartest-slider-track .weartest-card').each(function (i) {
            $(this).addClass('scroll-reveal stagger-' + ((i % 5) + 1));
        });

        $('.categories-grid .category-card, .categories-carousel .category-card, .category-grid .category-card').each(function (i) {
            $(this).addClass('scroll-reveal stagger-' + ((i % 4) + 1));
        });

        $('.vellajous-products-grid .vellajous-card, .products-grid .vellajous-card').each(function (i) {
            $(this).addClass('scroll-reveal stagger-' + ((i % 4) + 1));
        });

        $('.section-header, .attitude-header, .attitude-stats-bar, .weartest-sub-header, .promo-fast-delivery-banner, .customer-reviews-interactive, .perfume-guide__box, .story-grid > *, .spotlight-media, .spotlight-content, .newsletter-box').each(function () {
            $(this).addClass('scroll-reveal');
        });

        // IntersectionObserver for peak smooth scrolling performance
        if ('IntersectionObserver' in window) {
            var revealObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.05,
                rootMargin: '0px 0px -20px 0px'
            });

            document.querySelectorAll('.scroll-reveal, .scroll-reveal--left, .scroll-reveal--right, .scroll-reveal--scale').forEach(function (el) {
                var rect = el.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    el.classList.add('is-revealed');
                } else {
                    revealObserver.observe(el);
                }
            });
        } else {
            // Fallback for older browsers
            $('.scroll-reveal, .scroll-reveal--left, .scroll-reveal--right, .scroll-reveal--scale').addClass('is-revealed');
        }
    }

    /**
     * 8. Wear Test Video Reels Slider & Auto-Play Management
     * Ensures all reel videos auto-start muted on load and automatically slides smoothly
     */
    function initWeartestSlider() {
        var $viewport = $('#weartestViewport');
        var $track = $('#weartestTrack');
        var $prevBtn = $('#weartestPrevBtn');
        var $nextBtn = $('#weartestNextBtn');
        var $videos = $('.weartest-card__video');

        // 1. Smart Lazy Play for reel videos (Plays when section enters viewport to keep initial load instant)
        function playAllReelVideos() {
            var isMobile = window.innerWidth <= 768;
            $videos.each(function (idx) {
                var vid = this;
                vid.muted = true;
                if (isMobile && idx > 1) {
                    return; // On mobile, keep remaining videos on pause to ensure 100% fluid 120fps iOS scrolling
                }
                if (vid.paused) {
                    var playPromise = vid.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(function () {});
                    }
                }
            });
        }

        if ('IntersectionObserver' in window) {
            var videoObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        playAllReelVideos();
                    } else {
                        $videos.each(function () {
                            if (!this.paused) this.pause();
                        });
                    }
                });
            }, {
                rootMargin: '250px 0px 250px 0px'
            });

            var sectionEl = document.querySelector('.weartest-sub-section');
            if (sectionEl) {
                videoObserver.observe(sectionEl);
            }
        } else {
            playAllReelVideos();
        }

        if (!$viewport.length || !$track.length) return;

        var autoSlideTimer = null;
        var slideInterval = 3500; // 3.5 seconds
        var isHovered = false;

        function getStepWidth() {
            var $card = $track.find('.weartest-card').first();
            if (!$card.length) return 230;
            var gap = parseFloat($track.css('gap')) || 0;
            return $card.outerWidth() + gap;
        }

        function slideNext() {
            if (!$viewport.length) return;
            var currentScroll = $viewport.scrollLeft();
            var maxScroll = $viewport[0].scrollWidth - $viewport[0].clientWidth;
            var step = getStepWidth();

            if (currentScroll >= maxScroll - 15) {
                $viewport.stop().animate({ scrollLeft: 0 }, 600, 'swing');
            } else {
                $viewport.stop().animate({ scrollLeft: currentScroll + step }, 500, 'swing');
            }
        }

        function slidePrev() {
            if (!$viewport.length) return;
            var currentScroll = $viewport.scrollLeft();
            var maxScroll = $viewport[0].scrollWidth - $viewport[0].clientWidth;
            var step = getStepWidth();

            if (currentScroll <= 15) {
                $viewport.stop().animate({ scrollLeft: maxScroll }, 600, 'swing');
            } else {
                $viewport.stop().animate({ scrollLeft: currentScroll - step }, 500, 'swing');
            }
        }

        function startAutoSlide() {
            stopAutoSlide();
            if (!isHovered) {
                autoSlideTimer = setInterval(function () {
                    slideNext();
                }, slideInterval);
            }
        }

        function stopAutoSlide() {
            if (autoSlideTimer) {
                clearInterval(autoSlideTimer);
                autoSlideTimer = null;
            }
        }

        // Button events
        $nextBtn.on('click', function (e) {
            e.preventDefault();
            slideNext();
            startAutoSlide();
        });

        $prevBtn.on('click', function (e) {
            e.preventDefault();
            slidePrev();
            startAutoSlide();
        });

        // Pause on mouse hover or touch interaction
        $('.weartest-slider-wrap').on('mouseenter', function () {
            isHovered = true;
            stopAutoSlide();
        }).on('mouseleave', function () {
            isHovered = false;
            startAutoSlide();
        });

        // Start auto-slider initially
        startAutoSlide();
    }

    /**
     * 9. AJAX Load More for Shop & Category Archives (8 items per batch)
     */
    function initAjaxLoadMore() {
        $(document).on('click', '.vellajous-load-more-btn', function (e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.hasClass('is-loading')) return;

            var paged = parseInt($btn.attr('data-paged'), 10) || 2;
            var maxPages = parseInt($btn.attr('data-max-pages'), 10) || 1;
            var category = $btn.attr('data-category') || '';
            var orderby = $btn.attr('data-orderby') || 'menu_order';

            $btn.addClass('is-loading');

            $.ajax({
                url: vellajous_data.ajax_url,
                type: 'POST',
                data: {
                    action: 'vellajous_load_more_products',
                    paged: paged,
                    category: category,
                    orderby: orderby,
                    nonce: vellajous_data.nonce
                },
                success: function (res) {
                    $btn.removeClass('is-loading');
                    if (res.success && res.data && res.data.html) {
                        var $newCards = $(res.data.html);
                        $newCards.css({ opacity: 0 }).appendTo('.vellajous-shop-grid').animate({ opacity: 1 }, 450);

                        // Increment page
                        paged++;
                        $btn.attr('data-paged', paged);

                        // Update product count indicator if present
                        var currentCount = $('.vellajous-shop-grid .vellajous-card').length;
                        var totalFound = res.data.found_posts;
                        $('.vellajous-shop-count .count-text').text('Showing all ' + currentCount + ' of ' + totalFound + ' artisan creations');

                        // If no more pages, hide button
                        if (!res.data.has_more || paged > res.data.max_pages) {
                            $btn.fadeOut(350, function () {
                                $(this).parent().html('<div class="vellajous-load-more-end"><span>✨ All ' + totalFound + ' Artisan Creations Displayed</span></div>');
                            });
                        }
                    } else {
                        $btn.fadeOut(350);
                    }
                },
                error: function () {
                    $btn.removeClass('is-loading');
                }
            });
        });
    }

    /**
     * 10. Luxury Product Detail Page (PDP) Auto-Slide Gallery
     * Smooth cross-fade, auto-slide every 3.8s, pause on hover,
     * thumbnail & dot synchronization, swipe gestures for mobile.
     */
    function initProductGallery() {
        var $gallery = $('#vellajousPdpGallery');
        if (!$gallery.length) return;

        var $slides = $gallery.find('.vellajous-pdp-slide');
        var $thumbs = $gallery.find('.vellajous-pdp-thumb');
        var $dots = $gallery.find('.vellajous-pdp-dot');
        var $prevBtn = $gallery.find('.vellajous-pdp-arrow--prev');
        var $nextBtn = $gallery.find('.vellajous-pdp-arrow--next');
        var totalSlides = $slides.length;

        if (totalSlides <= 1) return;

        var currentIndex = 0;
        var autoSlideTimer = null;
        var slideInterval = 3800; // 3.8 seconds auto slide
        var isPaused = false;

        function goToSlide(index) {
            if (index < 0) {
                index = (index % totalSlides + totalSlides) % totalSlides;
            } else {
                index = index % totalSlides;
            }
            currentIndex = index;

            // Transition slides with smooth cross-fade
            $slides.removeClass('is-active');
            $slides.filter('[data-index="' + currentIndex + '"]').addClass('is-active');

            // Sync thumbnails
            $thumbs.removeClass('is-active');
            var $activeThumb = $thumbs.filter('[data-index="' + currentIndex + '"]').addClass('is-active');

            // Smooth scroll thumbnail into view if overflowing
            var $thumbsContainer = $('#pdpThumbs');
            if ($thumbsContainer.length && $activeThumb.length) {
                var containerLeft = $thumbsContainer.scrollLeft();
                var containerWidth = $thumbsContainer.width();
                var thumbLeft = $activeThumb.position().left + containerLeft;
                var thumbWidth = $activeThumb.outerWidth(true);

                if (thumbLeft < containerLeft || thumbLeft + thumbWidth > containerLeft + containerWidth) {
                    $thumbsContainer.stop().animate({
                        scrollLeft: thumbLeft - (containerWidth / 2) + (thumbWidth / 2)
                    }, 300);
                }
            }

            // Sync dot indicators
                        // Sync dot indicators
            $dots.removeClass('is-active');
            $dots.filter('[data-index="' + currentIndex + '"]').addClass('is-active');

            // Sync slide counter
            $('#pdpSlideCounter .pdp-counter-curr').text(currentIndex + 1);
        }

        function startAutoSlide() {
            stopAutoSlide();
            if (!isPaused) {
                autoSlideTimer = setInterval(function () {
                    goToSlide(currentIndex + 1);
                }, slideInterval);
            }
        }

        function stopAutoSlide() {
            if (autoSlideTimer) {
                clearInterval(autoSlideTimer);
                autoSlideTimer = null;
            }
        }

        // Thumbnails click
        $thumbs.on('click', function () {
            var idx = parseInt($(this).attr('data-index'), 10) || 0;
            goToSlide(idx);
            startAutoSlide();
        });

        // Dots click
        $dots.on('click', function () {
            var idx = parseInt($(this).attr('data-index'), 10) || 0;
            goToSlide(idx);
            startAutoSlide();
        });

        // Prev / Next arrow buttons
        $prevBtn.on('click', function (e) {
            e.preventDefault();
            goToSlide(currentIndex - 1);
            startAutoSlide();
        });

        $nextBtn.on('click', function (e) {
            e.preventDefault();
            goToSlide(currentIndex + 1);
            startAutoSlide();
        });

        // Pause auto-slide on hover, resume on mouse leave
        $gallery.on('mouseenter', function () {
            isPaused = true;
            stopAutoSlide();
        }).on('mouseleave', function () {
            isPaused = false;
            startAutoSlide();
        });

        // Mobile touch swipe gestures
        var touchStartX = 0;
        var touchStartY = 0;
        var $mainViewport = $gallery.find('.vellajous-pdp-gallery__main');

        $mainViewport.on('touchstart', function (e) {
            var touch = e.originalEvent.touches[0];
            touchStartX = touch.clientX;
            touchStartY = touch.clientY;
            isPaused = true;
            stopAutoSlide();
        });

        $mainViewport.on('touchend', function (e) {
            var touch = e.originalEvent.changedTouches[0];
            var diffX = touch.clientX - touchStartX;
            var diffY = touch.clientY - touchStartY;

            // Only trigger if horizontal swipe is prominent (> 40px) and greater than vertical scroll
            if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX < 0) {
                    goToSlide(currentIndex + 1);
                } else {
                    goToSlide(currentIndex - 1);
                }
            }

            isPaused = false;
            startAutoSlide();
        });

        // Kick off auto-slide immediately
        startAutoSlide();
    }

    /**
     * 11. Luxury Accordions for PDP Details & Fragrance Notes
     */
    function initProductAccordions() {
        var $accordions = $('#pdpAccordions');
        if (!$accordions.length) return;

        $accordions.on('click', '.vellajous-pdp-accordion__header', function () {
            var $header = $(this);
            var $item = $header.closest('.vellajous-pdp-accordion');
            var $body = $item.find('.vellajous-pdp-accordion__body');
            var isOpen = $item.hasClass('is-open');

            if (isOpen) {
                $body.stop().slideUp(260, function () {
                    $item.removeClass('is-open');
                    $header.attr('aria-expanded', 'false');
                });
            } else {
                // Close all other open accordions in the set
                $accordions.find('.vellajous-pdp-accordion.is-open').each(function () {
                    var $other = $(this);
                    $other.find('.vellajous-pdp-accordion__body').stop().slideUp(260, function () {
                        $other.removeClass('is-open');
                        $other.find('.vellajous-pdp-accordion__header').attr('aria-expanded', 'false');
                    });
                });

                // Open the clicked accordion
                $item.addClass('is-open');
                $header.attr('aria-expanded', 'true');
                $body.stop().slideDown(260);
            }
        });
    }

    /**
     * 12. PDP AJAX Add to Cart & Drawer Trigger
     */
    function initProductAddToCart() {
        var $form = $('#pdpCartForm');
        if (!$form.length) return;

        // Quantity Stepper Controls
        $form.on('click', '.qty-btn', function () {
            var $field = $form.find('.qty-field');
            var currentVal = parseInt($field.val(), 10) || 1;
            var maxVal = parseInt($field.attr('max'), 10) || 99;
            var minVal = parseInt($field.attr('min'), 10) || 1;

            if ($(this).hasClass('qty-plus')) {
                if (currentVal < maxVal) {
                    $field.val(currentVal + 1);
                }
            } else if ($(this).hasClass('qty-minus')) {
                if (currentVal > minVal) {
                    $field.val(currentVal - 1);
                }
            }

            // Sync with Buy Now form quantity
            $('.buynow-qty').val($field.val());
        });

        // Direct quantity input change
        $form.on('change input', '.qty-field', function () {
            var val = parseInt($(this).val(), 10) || 1;
            if (val < 1) val = 1;
            $(this).val(val);
            $('.buynow-qty').val(val);
        });

        // AJAX Add to Cart Submit
        $form.on('submit', function (e) {
            e.preventDefault();
            var $btn = $('#pdpAddToBag');
            if ($btn.hasClass('is-loading')) return;

            var productId = $form.find('input[name="product_id"]').val() || $form.find('input[name="add-to-cart"]').val();
            var quantity = $form.find('.qty-field').val() || 1;

            $btn.addClass('is-loading');

            var wcAjaxUrl = (typeof vellajous_data !== 'undefined' && vellajous_data.wc_ajax_url)
                ? vellajous_data.wc_ajax_url
                : (window.location.origin + '/?wc-ajax=add_to_cart');

            $.ajax({
                url: wcAjaxUrl,
                type: 'POST',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                success: function (res) {
                    $btn.removeClass('is-loading');

                    if (res && res.fragments) {
                        $.each(res.fragments, function (key, value) {
                            $(key).replaceWith(value);
                        });
                    }

                    $(document.body).trigger('wc_fragment_refresh');

                    // Automatically open luxury cart drawer
                    setTimeout(function () {
                        if (typeof openCartDrawer === 'function') {
                            openCartDrawer();
                        }
                    }, 250);
                },
                error: function () {
                    // Fallback to standard POST
                    $.ajax({
                        url: window.location.href,
                        type: 'POST',
                        data: $form.serialize(),
                        complete: function () {
                            $btn.removeClass('is-loading');
                            $(document.body).trigger('wc_fragment_refresh');
                            setTimeout(function () {
                                if (typeof openCartDrawer === 'function') {
                                    openCartDrawer();
                                }
                            }, 300);
                        }
                    });
                }
            });
        });
    }

    /**
     * 13. PDP Buy It Now Direct Checkout
     */
    function initProductBuyNow() {
        var $buyNowBtn = $('#pdpBuyNow');
        if (!$buyNowBtn.length) return;

        $buyNowBtn.on('click', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var $form = $('#pdpCartForm');
            var productId = $form.find('input[name="product_id"]').val() || $form.find('input[name="add-to-cart"]').val();
            var quantity = $form.find('.qty-field').val() || 1;

            $btn.css('opacity', '0.7').text('Proceeding to Checkout...');

            var checkoutUrl = (typeof vellajous_data !== 'undefined' && vellajous_data.cart_url)
                ? vellajous_data.cart_url.replace('/cart/', '/checkout/')
                : (window.location.origin + '/checkout/');

            var wcAjaxUrl = (typeof vellajous_data !== 'undefined' && vellajous_data.wc_ajax_url)
                ? vellajous_data.wc_ajax_url
                : (window.location.origin + '/?wc-ajax=add_to_cart');

            $.ajax({
                url: wcAjaxUrl,
                type: 'POST',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                complete: function () {
                    window.location.href = checkoutUrl;
                }
            });
        });
    }

    /**
     * 14. PDP Social Share & Copy Link with Toast Feedback
     */
    function initProductShare() {
        $(document).on('click', '.share-copy', function (e) {
            e.preventDefault();
            var url = $(this).attr('data-url') || window.location.href;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function () {
                    showCopyToast();
                }).catch(function () {
                    fallbackCopy(url);
                });
            } else {
                fallbackCopy(url);
            }
        });

        function fallbackCopy(text) {
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            showCopyToast();
        }

        function showCopyToast() {
            var $toast = $('.vellajous-copy-toast');
            if (!$toast.length) {
                $toast = $('<div class="vellajous-copy-toast">✨ Link Copied to Clipboard</div>');
                $('body').append($toast);
            }
            $toast.addClass('is-show');
            setTimeout(function () {
                $toast.removeClass('is-show');
            }, 2500);
        }
    }

    /**
     * 15. Luxury Product Detail Page Tabs
     */
    function initProductTabs() {
        var $tabs = $('#pdpTabsSection');
        if (!$tabs.length) return;

        $tabs.on('click', '.pdp-tab-btn', function () {
            var targetId = $(this).attr('data-tab');
            if (!targetId) return;

            $tabs.find('.pdp-tab-btn').removeClass('is-active').attr('aria-selected', 'false');
            $(this).addClass('is-active').attr('aria-selected', 'true');

            $tabs.find('.pdp-tab-pane').removeClass('is-active').hide();
            $('#' + targetId).fadeIn(240).addClass('is-active');

            // Smoothly center active tab in mobile horizontal scroll nav
            var $nav = $tabs.find('.vellajous-pdp-tabs-nav');
            if ($nav.length) {
                var navScrollLeft = $nav.scrollLeft();
                var navWidth = $nav.outerWidth();
                var btnLeft = $(this).position().left + navScrollLeft;
                var btnWidth = $(this).outerWidth();
                $nav.stop().animate({
                    scrollLeft: btnLeft - (navWidth / 2) + (btnWidth / 2)
                }, 280);
            }
        });

        // Click on top rating link smoothly switches to Reviews tab and scrolls down
        $(document).on('click', '.pdp-scroll-to-reviews, .pdp-switch-to-reviews-tab', function (e) {
            e.preventDefault();
            var $reviewsTabBtn = $tabs.find('.pdp-tab-btn[data-tab="pdp-tab-reviews"]');
            if ($reviewsTabBtn.length) {
                $reviewsTabBtn.trigger('click');
            }

            var offsetTop = $tabs.offset().top - 90;
            $('html, body').stop().animate({ scrollTop: offsetTop }, 600, 'swing');
        });

        // Click on "Write a Review" button scrolls directly to comment form
        $(document).on('click', '#toggleReviewFormBtn', function (e) {
            e.preventDefault();
            var $formWrap = $('#review_form_wrapper, #respond');
            if ($formWrap.length) {
                var formTop = $formWrap.offset().top - 100;
                $('html, body').stop().animate({ scrollTop: formTop }, 500, 'swing', function () {
                    $formWrap.find('textarea, input[type="text"]').first().focus();
                });
            }
        });
    }

    
    /**
     * 15b. Mobile Sticky Add-to-Bag Bar
     */
    function initStickyAddToCartBar() {
        var $stickyBar = $('#pdpStickyBar');
        var $cartForm = $('#pdpCartForm, .vellajous-pdp-cart-form');
        if (!$stickyBar.length || !$cartForm.length) return;

        var ticking = false;
        function checkSticky() {
            var formBottom = $cartForm.offset().top + $cartForm.outerHeight();
            var scrollTop = $(window).scrollTop();
            if (scrollTop > formBottom - 40) {
                $stickyBar.addClass('is-visible');
            } else {
                $stickyBar.removeClass('is-visible');
            }
            ticking = false;
        }

        $(window).on('scroll resize', function () {
            if (!ticking) {
                window.requestAnimationFrame(checkSticky);
                ticking = true;
            }
        });

        // Trigger add to cart from mobile sticky bar
        $('#pdpStickyAddBtn').on('click', function (e) {
            e.preventDefault();
            var $mainAddBtn = $('#pdpAddToBag');
            if ($mainAddBtn.length) {
                $mainAddBtn.trigger('click');
                var $btn = $(this);
                $btn.css('opacity', '0.7');
                setTimeout(function () {
                    $btn.css('opacity', '1');
                }, 600);
            }
        });
    }

    /**
     * 16. Contact Page Handlers: FAQ Accordions & AJAX Form Transmission
     */
    function initContactPage() {
        // FAQ Accordions Toggle
        $(document).on('click', '.faq-question', function (e) {
            e.preventDefault();
            var $item = $(this).closest('.faq-item');
            var $answer = $item.find('.faq-answer');
            var isOpen = $item.hasClass('is-open');

            // Close siblings for clean accordion behavior
            $item.siblings('.faq-item.is-open').removeClass('is-open').find('.faq-answer').slideUp(250).end().find('.faq-icon').text('+');

            if (isOpen) {
                $item.removeClass('is-open');
                $answer.slideUp(250);
                $(this).find('.faq-icon').text('+');
            } else {
                $item.addClass('is-open');
                $answer.slideDown(250);
                $(this).find('.faq-icon').text('−');
            }
        });

        // Contact Form AJAX Submission
        var $contactForm = $('#vellajousContactForm');
        if ($contactForm.length) {
            $contactForm.on('submit', function (e) {
                e.preventDefault();

                var $form = $(this);
                var $submitBtn = $form.find('#contactSubmitBtn');
                var $btnText = $submitBtn.find('.btn-text');
                var $btnArrow = $submitBtn.find('.btn-arrow');
                var $btnSpinner = $submitBtn.find('.btn-spinner');
                var $notice = $('#contactFormNotice');

                // UI loading state
                $submitBtn.prop('disabled', true).addClass('is-loading');
                $btnSpinner.show();
                $btnArrow.hide();
                $notice.slideUp(150).removeClass('notice-success notice-error').empty();

                var formData = $form.serialize();
                formData += '&action=vellajous_submit_contact';
                formData += '&security=' + encodeURIComponent($('#vellajous_contact_nonce_field').val());

                $.ajax({
                    url: (typeof vellajous_data !== 'undefined' && vellajous_data.ajax_url) ? vellajous_data.ajax_url : '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (res) {
                        $submitBtn.prop('disabled', false).removeClass('is-loading');
                        $btnSpinner.hide();
                        $btnArrow.show();

                        if (res && res.success) {
                            $notice.addClass('notice-success').html('<div class="notice-icon">✓</div><div class="notice-content"><h4>Inquiry Successfully Dispatched</h4><p>' + res.data.message + '</p></div>').slideDown(250);
                            $form[0].reset();
                        } else {
                            var errMsg = (res && res.data && res.data.message) ? res.data.message : 'An unexpected error occurred. Please try again.';
                            $notice.addClass('notice-error').html('<div class="notice-icon">!</div><div class="notice-content"><h4>Transmission Notice</h4><p>' + errMsg + '</p></div>').slideDown(250);
                        }
                    },
                    error: function () {
                        $submitBtn.prop('disabled', false).removeClass('is-loading');
                        $btnSpinner.hide();
                        $btnArrow.show();
                        $notice.addClass('notice-error').html('<div class="notice-icon">!</div><div class="notice-content"><h4>Network Notification</h4><p>Unable to connect to the atelier server. Please reach us via direct email at support@vellajous.com.</p></div>').slideDown(250);
                    }
                });
            });
        }
    }

    /**
     * 17. My Account Interactive Handlers: Auth Tabs & Password Toggle
     */
    function initMyAccountTabs() {
        // Tab switching between Sign In and Join Privé (Register)
        $(document).on('click', '.vellajous-auth-tabs .auth-tab', function (e) {
            e.preventDefault();
            var targetTab = $(this).data('tab');
            if (!targetTab) return;

            $('.vellajous-auth-tabs .auth-tab').removeClass('is-active');
            $(this).addClass('is-active');

            $('.vellajous-auth-panes .auth-pane').removeClass('is-active').hide();
            $('#pane-' + targetTab).fadeIn(220).addClass('is-active');
        });

        // Password Show/Hide Toggle
        $(document).on('click', '.password-toggle-btn', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var $wrap = $btn.closest('.input-wrap, .form-group');
            var $input = $wrap.find('input[type="password"], input[type="text"]').filter(function() {
                var name = $(this).attr('name') || '';
                return name.indexOf('password') !== -1 || $(this).attr('id') === 'password' || $(this).attr('id') === 'reg_password';
            }).first();

            if (!$input.length) {
                $input = $wrap.find('input[type="password"], input[type="text"]').first();
            }

            if ($input.length) {
                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $btn.html('<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>');
                } else {
                    $input.attr('type', 'password');
                    $btn.html('<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>');
                }
            }
        });
    }

    /**
     * 18. Instagram Carousel Handlers
     */
    function initInstagramCarousel() {
        var $track = $('#instaTrack');
        if (!$track.length) return;

        $('#instaPrevBtn').on('click', function (e) {
            e.preventDefault();
            var cardWidth = $track.find('.insta-card').first().outerWidth(true) || 240;
            $track.animate({ scrollLeft: $track.scrollLeft() - cardWidth }, 300);
        });

        $('#instaNextBtn').on('click', function (e) {
            e.preventDefault();
            var cardWidth = $track.find('.insta-card').first().outerWidth(true) || 240;
            $track.animate({ scrollLeft: $track.scrollLeft() + cardWidth }, 300);
        });
    }

    /**
     * 19. Custom Luxury Brand Cursor (Dot + Translucent Aura)
     * Smooth 60fps linear interpolation trailing effect
     */
    function initCustomCursor() {
        if (window.matchMedia('(pointer: coarse)').matches || 'ontouchstart' in window || navigator.maxTouchPoints > 0 || window.innerWidth < 1024) return;

        var $cursor = $('#vellajousCursor');
        var $dot = $('#vellajousCursorDot');
        var $circle = $('#vellajousCursorCircle');
        if (!$cursor.length || !$dot.length || !$circle.length) return;

        var mouseX = -100;
        var mouseY = -100;
        var circleX = -100;
        var circleY = -100;
        var isMoving = false;
        var rafId = null;

        $(window).on('mousemove', function (e) {
            mouseX = e.clientX;
            mouseY = e.clientY;

            $dot[0].style.transform = 'translate3d(' + mouseX + 'px, ' + mouseY + 'px, 0) translate(-50%, -50%)';

            if (!isMoving) {
                isMoving = true;
                circleX = mouseX;
                circleY = mouseY;
                $cursor.addClass('is-active');
                renderCursor();
            }
        });

        $(document).on('mouseleave', function () {
            $cursor.removeClass('is-active');
            isMoving = false;
            if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
        });

        function renderCursor() {
            if (!isMoving) return;
            var lerpSpeed = 0.18;
            circleX += (mouseX - circleX) * lerpSpeed;
            circleY += (mouseY - circleY) * lerpSpeed;

            $circle[0].style.transform = 'translate3d(' + circleX + 'px, ' + circleY + 'px, 0) translate(-50%, -50%)';

            rafId = requestAnimationFrame(renderCursor);
        }

        var interactiveSelectors = 'a, button, input, select, textarea, [role="button"], .button, .btn, .category-card, .vellajous-card, .weartest-card, .avatar-bubble, .header-action-btn, .custom-checkbox, .auth-tab';

        $(document).on('mouseenter', interactiveSelectors, function () {
            $cursor.addClass('is-hovering');
        });

        $(document).on('mouseleave', interactiveSelectors, function () {
            $cursor.removeClass('is-hovering');
        });

        $(document).on('mousedown', function () {
            $cursor.addClass('is-clicking');
        });

        $(document).on('mouseup', function () {
            $cursor.removeClass('is-clicking');
        });
    }

    /**
     * Mobile Bottom Navigation Bar & Search Overlay
     */
    function initBottomNav() {
        var $searchOverlay = $('#vellajousSearchOverlay');
        var $searchInput = $('#mobileSearchInput');

        $('#bottomNavSearchBtn').on('click', function (e) {
            e.preventDefault();
            $searchOverlay.addClass('is-open');
            setTimeout(function () {
                $searchInput.focus();
            }, 120);
        });

        $('#searchCloseBtn').on('click', function (e) {
            e.preventDefault();
            $searchOverlay.removeClass('is-open');
        });

        // Close on escape key
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $searchOverlay.hasClass('is-open')) {
                $searchOverlay.removeClass('is-open');
            }
        });

        // Close on clicking outside search form
        $(document).on('click', function (e) {
            if ($searchOverlay.hasClass('is-open') && !$(e.target).closest('#vellajousSearchOverlay, #bottomNavSearchBtn').length) {
                $searchOverlay.removeClass('is-open');
            }
        });
    }

    /**
     * 2-Step MCQ Fragrance Finder / Scent Quiz
     * Step 1: WHO ARE YOU? (4 Personas)
     * Step 2: HOW DO YOU FEEL? (4 Moods)
     * Dynamic shop product filtering + Scent Profiler Bar
     */
    function initScentQuiz() {
        var $modal = $('#scentQuizModal');
        if (!$modal.length) {
            return;
        }

        var $screen1 = $('#scentQuizScreen1');
        var $screen2 = $('#scentQuizScreen2');
        var $screenCurating = $('#scentQuizScreenCurating');
        var $progressBar = $('#scentQuizProgress');
        var $openBtn = $('#scentQuizOpenBtn');
        var $closeBtn = $('#scentQuizClose');
        var $backBtn = $('#scentQuizBackBtn');
        var $resetBtn = $('#scentQuizResetBtn');
        var $quizBar = $('#scentQuizBar');
        var $quizHeading = $('#scentQuizHeading');
        var $quizSubtext = $('#scentQuizSubtext');
        var $cards = $('.vellajous-products-grid .vellajous-card');
        var $countText = $('.vellajous-shop-count .count-text');
        var originalCountText = $countText.text();
        var $loadMoreWrap = $('.vellajous-load-more-wrap');

        var personaLabels = {
            'healthcare': 'Healthcare & Public Service',
            'business': 'Business & Professional',
            'design': 'Design & Build',
            'travel': 'Travel & Adventure'
        };

        var moodLabels = {
            'calm': 'Calm & Wind Down',
            'focus': 'Focus & Reset',
            'energy': 'Energy & Confidence',
            'bold': 'Bold & Magnetic'
        };

        var selectedPersona = null;
        var selectedMood = null;
        var selectedPersonaLabel = '';
        var selectedMoodLabel = '';

        function resetQuizScreens() {
            $modal.find('.quiz-choice-card').removeClass('is-selected');
            $screenCurating.removeClass('is-active');
            $screen2.removeClass('is-active');
            $screen1.addClass('is-active');
            $progressBar.css('width', '50%');
            selectedPersona = null;
            selectedMood = null;
        }

        function openModal() {
            resetQuizScreens();
            $modal.addClass('is-open').attr('aria-hidden', 'false');
            $('body').addClass('scent-quiz-open');
        }

        function closeModal() {
            $modal.removeClass('is-open').attr('aria-hidden', 'true');
            $('body').removeClass('scent-quiz-open');
        }

        // DO NOT auto-open quiz modal when visiting shop!
        // Instead, check URL query parameters (?persona=...&mood=...) and filter catalog directly
        var urlParams = new URLSearchParams(window.location.search);
        var qPersona = urlParams.get('persona');
        var qMood = urlParams.get('mood');

        if (qPersona || qMood) {
            var personaTitles = {
                'healthcare': 'Healthcare & Public Service',
                'business': 'Business & Professional',
                'design': 'Design & Build',
                'travel': 'Travel & Adventure'
            };
            var moodTitles = {
                'calm': 'Calm & Wind Down',
                'focus': 'Focus & Reset',
                'energy': 'Energy & Confidence',
                'bold': 'Bold & Magnetic'
            };

            var pLabel = personaTitles[qPersona] || (qPersona ? qPersona.toUpperCase() : '');
            var mLabel = moodTitles[qMood] || (qMood ? qMood.toUpperCase() : '');

            setTimeout(function () {
                applyProductFilter(qPersona || '', qMood || '', pLabel, mLabel);
            }, 80);
        }

        // Open button trigger from profiler bar
        $openBtn.on('click', function (e) {
            e.preventDefault();
            openModal();
        });

        // Close / Skip button
        $closeBtn.on('click', function (e) {
            e.preventDefault();
            closeModal();
        });

        // Close when clicking modal backdrop
        $modal.on('click', function (e) {
            if ($(e.target).is($modal)) {
                closeModal();
            }
        });

        // Close on ESC key
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $modal.hasClass('is-open')) {
                closeModal();
            }
        });

        // STEP 1 Selection: WHO ARE YOU?
        $screen1.on('click', '.quiz-choice-card', function (e) {
            e.preventDefault();
            var $this = $(this);
            $screen1.find('.quiz-choice-card').removeClass('is-selected');
            $this.addClass('is-selected');

            selectedPersona = $this.data('quiz-persona');
            selectedPersonaLabel = personaLabels[selectedPersona] || $this.find('.quiz-choice__title').text().trim();

            // Advance progress bar smoothly
            $progressBar.css('width', '100%');

            // Transition from Step 1 to Step 2
            setTimeout(function () {
                $screen1.removeClass('is-active');
                $screen2.addClass('is-active');
            }, 200);
        });

        // Back Button on Step 2 -> Returns to Step 1
        $backBtn.on('click', function (e) {
            e.preventDefault();
            $screen2.removeClass('is-active');
            $screen1.addClass('is-active');
            $progressBar.css('width', '50%');
        });

        // STEP 2 Selection: HOW DO YOU FEEL?
        $screen2.on('click', '.quiz-choice-card', function (e) {
            e.preventDefault();
            var $this = $(this);
            $screen2.find('.quiz-choice-card').removeClass('is-selected');
            $this.addClass('is-selected');

            selectedMood = $this.data('quiz-mood');
            selectedMoodLabel = moodLabels[selectedMood] || $this.find('.quiz-choice__title').text().trim();

            // Transition to Curating animation screen
            setTimeout(function () {
                $screen2.removeClass('is-active');
                $screenCurating.addClass('is-active');

                // Simulate luxury fragrance curation and execute filtering
                setTimeout(function () {
                    applyProductFilter(selectedPersona, selectedMood, selectedPersonaLabel, selectedMoodLabel);
                    closeModal();
                }, 750);
            }, 200);
        });

        // Apply dynamic product filtering on the shop page
        function applyProductFilter(persona, mood, personaName, moodName) {
            var matchedCards = [];
            var filterPersona = (persona || '').toLowerCase().trim();
            var filterMood = (mood || '').toLowerCase().trim();

            // First check for matches
            $cards.each(function () {
                var $card = $(this);
                var cardPersona = ($card.attr('data-persona') || '').toLowerCase();
                var cardMood = ($card.attr('data-mood') || '').toLowerCase();

                var personaMatch = !filterPersona || cardPersona.indexOf(filterPersona) !== -1;
                var moodMatch = !filterMood || cardMood.indexOf(filterMood) !== -1;

                if (personaMatch && moodMatch) {
                    matchedCards.push($card);
                }
            });

            // If strict matches are less than 2 and at least one filter was provided, include either match
            if (matchedCards.length < 2 && (filterPersona || filterMood)) {
                matchedCards = [];
                $cards.each(function () {
                    var $card = $(this);
                    var cardPersona = ($card.attr('data-persona') || '').toLowerCase();
                    var cardMood = ($card.attr('data-mood') || '').toLowerCase();

                    var personaMatch = filterPersona && cardPersona.indexOf(filterPersona) !== -1;
                    var moodMatch = filterMood && cardMood.indexOf(filterMood) !== -1;

                    if (personaMatch || moodMatch) {
                        matchedCards.push($card);
                    }
                });
            }

            // Fallback: If still none, show all cards
            if (matchedCards.length === 0) {
                $cards.each(function () {
                    matchedCards.push($(this));
                });
            }

            // Hide non-matching, show matching with animation
            $cards.each(function () {
                var $card = $(this);
                var isMatch = false;
                for (var i = 0; i < matchedCards.length; i++) {
                    if (matchedCards[i].is($card)) {
                        isMatch = true;
                        break;
                    }
                }

                if (isMatch) {
                    $card.removeClass('is-quiz-hidden').addClass('is-quiz-match');
                } else {
                    $card.addClass('is-quiz-hidden').removeClass('is-quiz-match');
                }
            });

            // Update Scent Profiler Bar
            $quizBar.addClass('is-filtered');
            var filterHeading = 'Curated Fragrances';
            if (personaName && moodName) {
                filterHeading = 'Curated For: ' + personaName + ' · ' + moodName;
            } else if (personaName) {
                filterHeading = 'Curated For: ' + personaName;
            } else if (moodName) {
                filterHeading = 'Curated For: ' + moodName;
            }

            $quizHeading.text(filterHeading);
            $quizSubtext.text('Displaying ' + matchedCards.length + ' bespoke creations matched to your selection.');
            $openBtn.find('span').text('Retake Scent Quiz');
            $resetBtn.show();

            // Update product counter
            $countText.text('Showing ' + matchedCards.length + ' curated artisan creations');

            // Hide pagination load-more during filtered state
            if ($loadMoreWrap.length) {
                $loadMoreWrap.hide();
            }

            // Smooth scroll to catalog / profiler bar
            if ($quizBar.length) {
                $('html, body').animate({
                    scrollTop: $quizBar.offset().top - 95
                }, 400);
            }
        }

        // Reset Filter / View All Fragrances
        $resetBtn.on('click', function (e) {
            e.preventDefault();
            $cards.removeClass('is-quiz-hidden is-quiz-match');
            $quizBar.removeClass('is-filtered');
            $quizHeading.text('Find Your Signature Scent');
            $quizSubtext.text('Answer 2 quick questions to discover your personalized fragrance match.');
            $openBtn.find('span').text('Take Scent Quiz');
            $resetBtn.hide();
            $countText.text(originalCountText);

            if ($loadMoreWrap.length) {
                $loadMoreWrap.show();
            }

            resetQuizScreens();
        });
    }

    /**
     * Standard Luxury Navigation Dropdowns
     * Desktop: Shop -> 2 Items (WHO ARE YOU? / HOW DO YOU FEEL?) -> 4 Items each
     * Mobile: Accordion expand/collapse
     */
    function initNavigationDropdowns() {
        // Mobile Drawer Accordion Toggle
        $(document).on('click', '.mobile-submenu-arrow', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $btn = $(this);
            var $row = $btn.closest('.mobile-nav-link-row');
            var $parentLi = $row.closest('li');
            var $sub = $row.next('ul');
            var isExpanded = $btn.attr('aria-expanded') === 'true';
            var willExpand = !isExpanded;

            $btn.attr('aria-expanded', willExpand);
            $btn.toggleClass('is-active', willExpand);
            $row.toggleClass('is-open', willExpand);
            $parentLi.toggleClass('is-open', willExpand);
            $sub.toggleClass('is-open', willExpand);
        });

        // Clicking anywhere on a mobile nav row with a submenu toggles the accordion
        $(document).on('click', '.mobile-nav-link-row', function (e) {
            if ($(e.target).closest('.mobile-submenu-arrow').length) return;
            var $btn = $(this).find('.mobile-submenu-arrow').first();
            if ($btn.length) {
                e.preventDefault();
                $btn.trigger('click');
            }
        });
    }

    /**
     * Bespoke Wishlist System
     * LocalStorage persistence + real-time badge sync + floating toast + Wishlist page AJAX
     */
    function initWishlist() {
        var STORAGE_KEY = 'vellajous_wishlist_ids';
        var toastTimer = null;

        function getWishlistIds() {
            try {
                var stored = localStorage.getItem(STORAGE_KEY);
                if (!stored) return [];
                var parsed = JSON.parse(stored);
                if (Array.isArray(parsed)) {
                    return parsed.map(function (id) {
                        return parseInt(id, 10);
                    }).filter(function (id) {
                        return !isNaN(id) && id > 0;
                    });
                }
                return [];
            } catch (e) {
                return [];
            }
        }

        function saveWishlistIds(ids) {
            try {
                var unique = [];
                for (var i = 0; i < ids.length; i++) {
                    var num = parseInt(ids[i], 10);
                    if (!isNaN(num) && num > 0 && unique.indexOf(num) === -1) {
                        unique.push(num);
                    }
                }
                localStorage.setItem(STORAGE_KEY, JSON.stringify(unique));
                updateWishlistUI(unique);
                return unique;
            } catch (e) {
                return ids;
            }
        }

        function updateWishlistUI(ids) {
            if (!ids) ids = getWishlistIds();
            var count = ids.length;

            // Badges in Header, Bottom Nav, Drawer, and Wishlist Page
            $('.vellajous-wishlist-count, .bottom-nav-badge, #wishlistTotalCount').each(function () {
                var $badge = $(this);
                $badge.text(count);
                if (count > 0) {
                    $badge.css('display', 'flex').show();
                } else {
                    $badge.hide();
                }
            });

            if (count > 0) {
                $('.header-wishlist-btn, .bottom-nav-wishlist-btn').addClass('has-items');
            } else {
                $('.header-wishlist-btn, .bottom-nav-wishlist-btn').removeClass('has-items');
            }

            // Sync all wishlist buttons
            $('.vellajous-wishlist-btn').each(function () {
                var $btn = $(this);
                var pId = parseInt($btn.data('product-id'), 10);
                var isWishlisted = ids.indexOf(pId) !== -1;

                if (isWishlisted) {
                    $btn.addClass('is-active');
                    $btn.attr('aria-pressed', 'true');
                    if ($btn.find('.wishlist-btn-label').length) {
                        $btn.find('.wishlist-btn-label').text('Added to Wishlist');
                    }
                } else {
                    $btn.removeClass('is-active');
                    $btn.attr('aria-pressed', 'false');
                    if ($btn.find('.wishlist-btn-label').length) {
                        $btn.find('.wishlist-btn-label').text('Add to Wishlist');
                    }
                }
            });
        }

        function showWishlistToast(productName, isAdded) {
            var $toast = $('#vellajousWishlistToast');
            if (!$toast.length) {
                $toast = $(
                    '<div id="vellajousWishlistToast" class="vellajous-toast" role="status" aria-live="polite">' +
                        '<span class="toast-icon-wrap">' +
                            '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>' +
                        '</span>' +
                        '<span class="toast-message"></span>' +
                    '</div>'
                );
                $('body').append($toast);
            }

            var cleanName = (productName || 'Fragrance').trim();
            var msg = isAdded ?
                (cleanName + ' added to your Wishlist') :
                (cleanName + ' removed from your Wishlist');

            $toast.find('.toast-message').text(msg);
            $toast.toggleClass('toast--removed', !isAdded);
            $toast.addClass('is-visible');

            if (toastTimer) clearTimeout(toastTimer);
            toastTimer = setTimeout(function () {
                $toast.removeClass('is-visible');
            }, 2400);
        }

        // Toggle Wishlist on click
        $(document).on('click', '.vellajous-wishlist-btn', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn = $(this);
            var pId = parseInt($btn.data('product-id'), 10);
            if (!pId || isNaN(pId)) return;

            var pTitle = $btn.data('product-title');
            if (!pTitle) {
                pTitle = $btn.closest('.vellajous-card, .vellajous-pdp, .spotlight-highlight-card').find('.vellajous-card__title, .vellajous-pdp__title, .product_title, .spotlight-title').first().text().trim() || 'Fragrance';
            }

            var ids = getWishlistIds();
            var idx = ids.indexOf(pId);
            var isAdded = false;

            if (idx === -1) {
                ids.push(pId);
                isAdded = true;
            } else {
                ids.splice(idx, 1);
                isAdded = false;
            }

            // Animate button
            $btn.addClass('is-animating');
            setTimeout(function () {
                $btn.removeClass('is-animating');
            }, 350);

            saveWishlistIds(ids);
            showWishlistToast(pTitle, isAdded);

            // Animate badge
            $('.bottom-nav-badge, .vellajous-wishlist-count').addClass('is-animating');
            setTimeout(function () {
                $('.bottom-nav-badge, .vellajous-wishlist-count').removeClass('is-animating');
            }, 300);

            // Wishlist page dynamic card removal
            if ($('.vellajous-wishlist-page').length && !isAdded) {
                var $card = $btn.closest('.vellajous-card');
                if ($card.length) {
                    $card.fadeOut(260, function () {
                        $(this).remove();
                        if (ids.length === 0) {
                            $('#wishlistToolbar').hide();
                            $('#wishlistGrid').hide();
                            $('#wishlistEmpty').fadeIn(260);
                        }
                    });
                }
            }
        });

        // Wishlist Dedicated Page Rendering
        function initWishlistPage() {
            var $wishlistPage = $('.vellajous-wishlist-page');
            if (!$wishlistPage.length) return;

            var $toolbar = $('#wishlistToolbar');
            var $loading = $('#wishlistLoading');
            var $grid    = $('#wishlistGrid');
            var $empty   = $('#wishlistEmpty');

            var ids = getWishlistIds();

            if (ids.length === 0) {
                $loading.hide();
                $toolbar.hide();
                $grid.hide();
                $empty.show();
                return;
            }

            $empty.hide();
            $toolbar.show();
            $loading.show();
            $grid.hide();

            var ajaxUrl = (typeof vellajous_data !== 'undefined' && vellajous_data.ajax_url) ? 
                vellajous_data.ajax_url : 
                '/saurabh-task/vellajous/wp-admin/admin-ajax.php';

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'vellajous_get_wishlist_items',
                    ids: ids
                },
                success: function (res) {
                    $loading.hide();
                    if (res && res.success && res.data && res.data.html) {
                        $grid.html(res.data.html).show();
                        updateWishlistUI(ids);
                    } else {
                        $grid.hide();
                        $toolbar.hide();
                        $empty.show();
                    }
                },
                error: function () {
                    $loading.hide();
                    $grid.hide();
                    $toolbar.hide();
                    $empty.show();
                }
            });

            // Clear all button handler
            $('#wishlistClearAll').off('click').on('click', function (e) {
                e.preventDefault();
                saveWishlistIds([]);
                $grid.slideUp(200, function () {
                    $(this).empty();
                });
                $toolbar.hide();
                $empty.fadeIn(250);
                showWishlistToast('Wishlist', false);
            });
        }

        // Initialize UI & Page loader
        updateWishlistUI();
        initWishlistPage();
    }

    /**
     * 26. Luxury Checkout & Payment Experience Enhancements
     */
    function initCheckoutEnhancements() {
        var $checkoutForm = $('form.woocommerce-checkout');
        if (!$checkoutForm.length) return;

        function updateSelectedPayment() {
            var $checked = $('input[name="payment_method"]:checked');
            $('ul.payment_methods li').removeClass('is-selected');
            $checked.closest('li').addClass('is-selected');
        }

        // On payment method change
        $(document.body).on('change', 'input[name="payment_method"]', function () {
            updateSelectedPayment();
        });

        // On updated checkout event
        $(document.body).on('updated_checkout', function () {
            updateSelectedPayment();
        });

        // Run on initial load
        updateSelectedPayment();

        // Smooth scroll to notices if validation fails
        $(document.body).on('checkout_error', function () {
            var $notices = $('.woocommerce-NoticeGroup-checkout, .woocommerce-error');
            if ($notices.length) {
                $('html, body').stop().animate({
                    scrollTop: $notices.first().offset().top - 120
                }, 400);
            }
        });
    }

})(jQuery);


