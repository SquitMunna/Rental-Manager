<?php
if (!defined('ABSPATH')) exit;

function rntmgr_register_post_types() {
    // Locations
    register_post_type('location', [
        'label' => __('Locations', 'rental-manager'),
        'labels' => [
            'name' => __('Locations', 'rental-manager'),
            'singular_name' => __('Location', 'rental-manager'),
            'add_new' => __('Add New', 'rental-manager'),
            'add_new_item' => __('Add New Location', 'rental-manager'),
            'edit_item' => __('Edit Location', 'rental-manager'),
            'new_item' => __('New Location', 'rental-manager'),
            'view_item' => __('View Location', 'rental-manager'),
            'search_items' => __('Search Locations', 'rental-manager'),
        ],
        'public' => true,
        'menu_icon' => 'dashicons-location',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);

    // Buildings
    register_post_type('building', [
        'label' => __('Buildings', 'rental-manager'),
        'labels' => [
            'name' => __('Buildings', 'rental-manager'),
            'singular_name' => __('Building', 'rental-manager'),
            'add_new' => __('Add New', 'rental-manager'),
            'add_new_item' => __('Add New Building', 'rental-manager'),
            'edit_item' => __('Edit Building', 'rental-manager'),
            'new_item' => __('New Building', 'rental-manager'),
            'view_item' => __('View Building', 'rental-manager'),
            'search_items' => __('Search Buildings', 'rental-manager'),
        ],
        'public' => true,
        'menu_icon' => 'dashicons-building',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);

    // Rooms
    register_post_type('room', [
        'label' => __('Rooms', 'rental-manager'),
        'labels' => [
            'name' => __('Rooms', 'rental-manager'),
            'singular_name' => __('Room', 'rental-manager'),
            'add_new' => __('Add New', 'rental-manager'),
            'add_new_item' => __('Add New Room', 'rental-manager'),
            'edit_item' => __('Edit Room', 'rental-manager'),
            'new_item' => __('New Room', 'rental-manager'),
            'view_item' => __('View Room', 'rental-manager'),
            'search_items' => __('Search Rooms', 'rental-manager'),
        ],
        'public' => true,
        'menu_icon' => 'dashicons-admin-home',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
        'has_archive' => true,
    ]);

    // Bookings
    register_post_type('booking', [
        'label' => __('Bookings', 'rental-manager'),
        'labels' => [
            'name' => __('Bookings', 'rental-manager'),
            'singular_name' => __('Booking', 'rental-manager'),
            'add_new' => __('Add New', 'rental-manager'),
            'add_new_item' => __('Add New Booking', 'rental-manager'),
            'edit_item' => __('Edit Booking', 'rental-manager'),
            'new_item' => __('New Booking', 'rental-manager'),
            'view_item' => __('View Booking', 'rental-manager'),
            'search_items' => __('Search Bookings', 'rental-manager'),
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-clipboard',
        'supports' => ['title'],
        'map_meta_cap' => true,
    ]);
    
    // Custom statuses for bookings
    
    foreach (rntmgr_booking_statuses() as $status => $label) {
        register_post_status($status, [
            'label' => $label,
            'public' => false,
            'internal' => false,
            'protected' => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list' => true,
            'label_count' => _n_noop("$label <span class='count'>(%s)</span>", "$label <span class='count'>(%s)</span>", 'rental-manager'),
        ]);
    }
    
}
add_action('init', 'rntmgr_register_post_types');

