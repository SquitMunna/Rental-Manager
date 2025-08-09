<?php
if (!defined('ABSPATH')) exit;

function rntmgr_enqueue_booking_assets() {
    // Register script
    wp_register_script(
        'rntmgr-booking',
        RNTMGR_PLUGIN_URL . 'assets/js/booking.js',
        ['jquery'], // ensures jQuery loads first
        '1.0.0',
        true // in footer
    );

    // Pass data to JS (AJAX URL + security nonce)
    wp_localize_script('rntmgr-booking', 'rntmgr_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('rntmgr_booking_nonce'),
    ]);

    // Enqueue it
    wp_enqueue_script('rntmgr-booking');
}

/**
 * Only load the script on pages that contain the booking form shortcode.
 * This avoids loading JS site‑wide.
 */
function rntmgr_maybe_enqueue_booking_assets() {
    if (is_singular()) {
        global $post;
        if ($post && has_shortcode($post->post_content, 'rntmgr_booking_form')) {
            rntmgr_enqueue_booking_assets();
        }
    }
}
add_action('wp_enqueue_scripts', 'rntmgr_maybe_enqueue_booking_assets');
