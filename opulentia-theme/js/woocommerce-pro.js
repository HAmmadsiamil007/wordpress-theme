/**
 * WooCommerce Pro Enhancements
 *
 * Quick View Modal, AJAX Add-to-Cart, Image Gallery Zoom,
 * Mini Cart Dropdown, Wishlist Toggle
 *
 * @package opulentia
 */
(function ($) {
    'use strict';

    // =========================================================================
    // Quick View Modal
    // =========================================================================

    function initQuickView() {
        // Delegate click on quick-view buttons (including future ones)
        $(document).on('click', '.quick-view-btn', function (e) {
            e.preventDefault();
            var productId = $(this).data('product-id');
            if (!productId) return;
            loadQuickView(productId);
        });

        // Close modal on overlay click
        $(document).on('click', '.quick-view-overlay', function () {
            closeQuickView();
        });

        // Close on escape
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                closeQuickView();
            }
        });

        // Close on close button
        $(document).on('click', '.quick-view__close', function () {
            closeQuickView();
        });
    }

    function loadQuickView(productId) {
        var overlay = $('.quick-view-overlay');
        if (!overlay.length) {
            // Create modal if not in DOM
            $('body').append(
                '<div class="quick-view-overlay">' +
                    '<div class="quick-view-modal">' +
                        '<button class="quick-view__close" aria-label="' + opulentiaWcPro.closeText + '">' +
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>' +
                        '</button>' +
                        '<div class="quick-view__content"></div>' +
                    '</div>' +
                '</div>'
            );
            overlay = $('.quick-view-overlay');
        }

        overlay.addClass('is-loading');
        overlay.fadeIn(200);
        $('body').addClass('quick-view-open');

        // AJAX load product data
        $.ajax({
            url: opulentiaWcPro.ajaxUrl,
            type: 'POST',
            data: {
                action: 'Opulentia_quick_view',
                product_id: productId,
                nonce: opulentiaWcPro.nonce
            },
            success: function (response) {
                overlay.removeClass('is-loading');
                if (response.success) {
                    $('.quick-view__content').html(response.data.html);
                    // Reinitialize WooCommerce variation form
                    $(document.body).trigger('wc_variation_form');
                    // Re-trigger image zoom on quick view gallery
                    initImageZoom();
                } else {
                    $('.quick-view__content').html('<p>' + opulentiaWcPro.errorText + '</p>');
                }
            },
            error: function () {
                overlay.removeClass('is-loading');
                $('.quick-view__content').html('<p>' + opulentiaWcPro.errorText + '</p>');
            }
        });
    }

    function closeQuickView() {
        $('.quick-view-overlay').fadeOut(200);
        $('body').removeClass('quick-view-open');
        setTimeout(function () {
            $('.quick-view__content').empty();
        }, 300);
    }

    // =========================================================================
    // AJAX Add-to-Cart
    // =========================================================================

    function initAjaxAddToCart() {
        $(document).on('click', '.opulentia-ajax-add-to-cart', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var productId = $btn.data('product-id');
            var quantity = $btn.data('quantity') || 1;

            if (!productId) return;

            $btn.addClass('loading');
            $btn.prop('disabled', true);

            $.ajax({
                url: opulentiaWcPro.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'Opulentia_ajax_add_to_cart',
                    product_id: productId,
                    quantity: quantity,
                    nonce: opulentiaWcPro.nonce
                },
                success: function (response) {
                    $btn.removeClass('loading');
                    $btn.prop('disabled', false);

                    if (response.success) {
                        // Update mini cart
                        if (typeof updateMiniCart === 'function') {
                            updateMiniCart();
                        }
                        // Update header cart count
                        updateCartCount();
                        // Show success feedback
                        $btn.addClass('added');
                        setTimeout(function () {
                            $btn.removeClass('added');
                        }, 2000);
                        // Trigger WooCommerce event
                        $(document.body).trigger('added_to_cart', [response.data.fragments, response.data.cart_hash, $btn]);
                    } else {
                        // Show error
                        alert(response.data.message || opulentiaWcPro.errorText);
                    }
                },
                error: function () {
                    $btn.removeClass('loading');
                    $btn.prop('disabled', false);
                    alert(opulentiaWcPro.errorText);
                }
            });
        });
    }

    function updateCartCount() {
        $.ajax({
            url: opulentiaWcPro.ajaxUrl,
            type: 'POST',
            data: {
                action: 'Opulentia_cart_count',
                nonce: opulentiaWcPro.nonce
            },
            success: function (response) {
                if (response.success) {
                    $('.cart-count').text(response.data.count);
                    if (response.data.count > 0) {
                        $('.cart-count').show();
                    } else {
                        $('.cart-count').hide();
                    }
                }
            }
        });
    }

    // =========================================================================
    // Mini Cart Dropdown
    // =========================================================================

    function initMiniCart() {
        var $cartBtn = $('.header-actions__btn--cart');
        var $miniCart = $('.mini-cart-dropdown');

        if (!$cartBtn.length || !$miniCart.length) return;

        $cartBtn.on('click', function (e) {
            // Allow the link to navigate to cart page, but also toggle mini cart
            if ($(window).width() > 768) {
                e.preventDefault();
                $miniCart.toggleClass('is-open');
            }
        });

        // Close mini cart on outside click
        $(document).on('click', function (e) {
            if (!$miniCart.is(e.target) && !$miniCart.has(e.target).length && !$cartBtn.is(e.target) && !$cartBtn.has(e.target).length) {
                $miniCart.removeClass('is-open');
            }
        });
    }

    function updateMiniCart() {
        $.ajax({
            url: opulentiaWcPro.ajaxUrl,
            type: 'POST',
            data: {
                action: 'Opulentia_mini_cart',
                nonce: opulentiaWcPro.nonce
            },
            success: function (response) {
                if (response.success) {
                    $('.mini-cart-dropdown').html(response.data.html);
                }
            }
        });
    }

    // =========================================================================
    // Image Gallery Zoom
    // =========================================================================

    function initImageZoom() {
        var $gallery = $('.single-product__gallery .woocommerce-product-gallery');
        if (!$gallery.length) return;

        var $mainImage = $gallery.find('.woocommerce-product-gallery__image img');
        if (!$mainImage.length) return;

        // Wrap main image in zoom container
        var $zoomWrap = $mainImage.closest('.woocommerce-product-gallery__image');
        if (!$zoomWrap.length) return;

        // Add zoom lens element
        if (!$zoomWrap.find('.product-zoom-lens').length) {
            $zoomWrap.append('<div class="product-zoom-lens"></div>');
        }

        var $lens = $zoomWrap.find('.product-zoom-lens');

        $zoomWrap.on('mousemove', function (e) {
            var rect = $zoomWrap[0].getBoundingClientRect();
            var x = (e.clientX - rect.left) / rect.width;
            var y = (e.clientY - rect.top) / rect.height;

            // Clamp values
            x = Math.max(0, Math.min(1, x));
            y = Math.max(0, Math.min(1, y));

            // Position lens
            var lensSize = 100;
            var lensX = (x * rect.width) - (lensSize / 2);
            var lensY = (y * rect.height) - (lensSize / 2);
            lensX = Math.max(0, Math.min(rect.width - lensSize, lensX));
            lensY = Math.max(0, Math.min(rect.height - lensSize, lensY));

            $lens.css({
                left: lensX + 'px',
                top: lensY + 'px',
                opacity: 1
            });

            // Zoom the image by scaling and translating
            var imgW = $mainImage.width();
            var imgH = $mainImage.height();
            var bgX = -(x * (imgW * 2 - rect.width));
            var bgY = -(y * (imgH * 2 - rect.height));

            $lens.css({
                backgroundImage: 'url(' + $mainImage.attr('src') + ')',
                backgroundSize: (imgW * 2) + 'px ' + (imgH * 2) + 'px',
                backgroundPosition: bgX + 'px ' + bgY + 'px'
            });
        });

        $zoomWrap.on('mouseleave', function () {
            $lens.css({ opacity: 0 });
        });
    }

    // =========================================================================
    // Wishlist Toggle
    // =========================================================================

    function initWishlist() {
        // Load wishlist from localStorage
        var wishlist = getWishlist();

        // Set initial states
        updateWishlistButtons(wishlist);

        // Toggle on click
        $(document).on('click', '.wishlist-toggle', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn = $(this);
            var productId = parseInt($btn.data('product-id'));
            if (!productId) return;

            var wishlist = getWishlist();
            var index = wishlist.indexOf(productId);

            if (index > -1) {
                wishlist.splice(index, 1);
                $btn.removeClass('in-wishlist');
                $btn.attr('aria-label', opulentiaWcPro.addToWishlistText);
            } else {
                wishlist.push(productId);
                $btn.addClass('in-wishlist');
                $btn.attr('aria-label', opulentiaWcPro.removeFromWishlistText);
            }

            saveWishlist(wishlist);
            updateWishlistCount(wishlist.length);
        });
    }

    function getWishlist() {
        try {
            var data = localStorage.getItem('Opulentia_wishlist');
            return data ? JSON.parse(data) : [];
        } catch (e) {
            return [];
        }
    }

    function saveWishlist(wishlist) {
        try {
            localStorage.setItem('Opulentia_wishlist', JSON.stringify(wishlist));
        } catch (e) {
            // localStorage not available
        }
    }

    function updateWishlistButtons(wishlist) {
        $('.wishlist-toggle').each(function () {
            var $btn = $(this);
            var productId = parseInt($btn.data('product-id'));
            if (wishlist.indexOf(productId) > -1) {
                $btn.addClass('in-wishlist');
                $btn.attr('aria-label', opulentiaWcPro.removeFromWishlistText);
            } else {
                $btn.removeClass('in-wishlist');
                $btn.attr('aria-label', opulentiaWcPro.addToWishlistText);
            }
        });
    }

    function updateWishlistCount(count) {
        var $count = $('.wishlist-count');
        if ($count.length) {
            $count.text(count);
            if (count > 0) {
                $count.show();
            } else {
                $count.hide();
            }
        }
    }

    // =========================================================================
    // Sticky Add-to-Cart
    // =========================================================================

    function initStickyAddToCart() {
        var $stickyBar = $('.sticky-add-to-cart');
        if (!$stickyBar.length) return;

        var $addToCartForm = $('.single-product .cart, .single-product .variations_form.cart');
        var $mainAddToCart = $('.single_add_to_cart_button');

        if (!$mainAddToCart.length) {
            $stickyBar.remove();
            return;
        }

        // Sync sticky button click with main add-to-cart
        $stickyBar.on('click', '.sticky-add-to-cart__button', function (e) {
            e.preventDefault();

            // If there's a variation form, scroll to it
            if ($addToCartForm.length) {
                var offset = $addToCartForm.offset().top - 120;
                $('html, body').animate({ scrollTop: offset }, 400);
                return;
            }

            // For simple products, simulate main button click
            $mainAddToCart.trigger('click');
        });

        // Sync quantity input
        $stickyBar.on('change keyup', '.sticky-add-to-cart__qty', function () {
            var qty = $(this).val();
            $('.quantity .qty').val(qty).trigger('change');
        });

        // Reverse sync from main quantity
        $(document).on('change', '.quantity .qty', function () {
            var qty = $(this).val();
            $('.sticky-add-to-cart__qty').val(qty);
        });

        // Show/hide on scroll
        var $productSummary = $('.product-summary, .entry-summary');
        var stickyThreshold = $mainAddToCart.offset().top + $mainAddToCart.outerHeight();

        $(window).on('scroll resize', function () {
            var scrollTop = $(window).scrollTop();
            var showThreshold = $productSummary.length
                ? $productSummary.offset().top + $productSummary.outerHeight()
                : stickyThreshold;

            if (scrollTop > showThreshold) {
                $stickyBar.addClass('is-visible');
            } else {
                $stickyBar.removeClass('is-visible');
            }
        });

        // Watch for variation changes to sync price
        $(document).on('found_variation', function (event, variation) {
            if (variation && variation.price_html) {
                $('.sticky-add-to-cart__price').html(variation.price_html);
            }
        });

        $(document).on('reset_data', function () {
            // Reset to default price on variation clear
            var defaultPrice = $('.sticky-add-to-cart').data('default-price');
            if (defaultPrice) {
                $('.sticky-add-to-cart__price').html(defaultPrice);
            }
        });
    }

    // =========================================================================
    // Initialization
    // =========================================================================

    $(document).ready(function () {
        if (typeof opulentiaWcPro === 'undefined') return;

        initQuickView();
        initAjaxAddToCart();
        initMiniCart();
        initImageZoom();
        initWishlist();
        initStickyAddToCart();

        // Re-init on WooCommerce variation form updates
        $(document).on('woocommerce_variation_has_changed', function () {
            setTimeout(function () {
                initImageZoom();
            }, 200);
        });
    });

})(jQuery);
