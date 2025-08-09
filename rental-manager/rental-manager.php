<?php
/**
 * Plugin Name: Rental Manager (Locations, Buildings, Rooms, Bookings)
 * Description: Locations → Buildings → Rooms with bookings, availability, emails, and admin approvals.
 * Version: 1.0.0
 * Author: Md Kawsar Munna
 * Text Domain: rental-manager
 */

if (!defined('ABSPATH')) exit;
if (!defined('RNTMGR_PLUGIN_PATH')) {
    define('RNTMGR_PLUGIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('RNTMGR_PLUGIN_URL')) {
    define('RNTMGR_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('RNTMGR_PATH')) {
    define('RNTMGR_PATH', plugin_dir_path(__FILE__));
}   
define('RNTMGR_VERSION', '1.0.0');
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax.php';
// Include modules
require_once RNTMGR_PLUGIN_PATH . 'includes/assets.php';
require_once RNTMGR_PLUGIN_PATH . 'includes/ajax.php';
require_once RNTMGR_PLUGIN_PATH . 'includes/shortcodes.php';
require_once RNTMGR_PATH . 'includes/helpers.php';
require_once RNTMGR_PATH . 'includes/post-types.php';
require_once RNTMGR_PATH . 'includes/meta-boxes.php';
require_once RNTMGR_PATH . 'includes/enqueue.php';
require_once RNTMGR_PATH . 'includes/booking-handler.php';
require_once RNTMGR_PATH . 'includes/admin.php';
require_once RNTMGR_PATH . 'includes/email.php';

register_activation_hook(__FILE__, function() {
    rntmgr_register_post_types();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});
