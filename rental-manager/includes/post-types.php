<?php
function rntmgr_register_post_types() {

    // Location
    register_post_type('location', [
        'labels' => [
            'name' => 'Locations',
            'singular_name' => 'Location',
            'add_new' => 'Add New Location',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-location-alt',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_menu' => true,
    ]);

    // Building
    register_post_type('building', [
        'labels' => [
            'name' => 'Buildings',
            'singular_name' => 'Building',
            'add_new' => 'Add New Building',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-admin-home',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_menu' => true,
    ]);

    // Room
    register_post_type('room', [
        'labels' => [
            'name' => 'Rooms',
            'singular_name' => 'Room',
            'add_new' => 'Add New Room',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 7,
        'menu_icon' => 'dashicons-admin-multisite',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_menu' => true,
    ]);

    // Booking
    register_post_type('booking', [
        'labels' => [
            'name' => 'Bookings',
            'singular_name' => 'Booking',
            'add_new' => 'Add New Booking',
        ],
        'public' => true,
        'has_archive' => false,
        'menu_position' => 8,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => ['title', 'editor'],
        'show_in_menu' => true,
    ]);
}
add_action('init', 'rntmgr_register_post_types');
