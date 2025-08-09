<?php
if (!defined('ABSPATH')) exit;

add_action('admin_enqueue_scripts', function($hook) {
    // Enable media frame for gallery
    wp_enqueue_media();
    wp_enqueue_script('rntmgr-admin', RNTMGR_URL . 'assets/js/front.js', ['jquery'], RNTMGR_VERSION, true);
});

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('rntmgr-front', RNTMGR_URL . 'assets/css/front.css', [], RNTMGR_VERSION);
    wp_enqueue_script('rntmgr-front', RNTMGR_URL . 'assets/js/front.js', ['jquery'], RNTMGR_VERSION, true);
    wp_localize_script('rntmgr-front', 'rntmgr', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('rntmgr_nonce')
    ]);
});
