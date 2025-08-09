<?php
if (!defined('ABSPATH')) exit;

function rntmgr_sanitize_date($date) {
    // Expect Y-m-d from frontend
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d ? $d->format('Y-m-d') : '';
}

function rntmgr_price_format($amount) {
    $amount = floatval($amount);
    return number_format($amount, 2);
}

function rntmgr_get_setting($key, $default = '') {
    return apply_filters('rntmgr_setting_' . $key, $default);
}

function rntmgr_get_room_price($room_id) {
    return get_post_meta($room_id, 'room_price', true);
}

function rntmgr_get_room_location_id($room_id) {
    return intval(get_post_meta($room_id, 'room_location_id', true));
}

function rntmgr_get_room_building_id($room_id) {
    return intval(get_post_meta($room_id, 'room_building_id', true));
}

function rntmgr_get_related_rooms($room_id, $limit = 3) {
    $building_id = rntmgr_get_room_building_id($room_id);
    $args = [
        'post_type' => 'room',
        'posts_per_page' => $limit,
        'post__not_in' => [$room_id],
        'meta_query' => [
            [
                'key' => 'room_building_id',
                'value' => $building_id,
                'compare' => '='
            ]
        ]
    ];
    return get_posts($args);
}

function rntmgr_booking_statuses() {
    return [
        'rental_pending'  => __('Pending', 'rental-manager'),
        'rental_approved' => __('Approved', 'rental-manager'),
        'rental_rejected' => __('Rejected', 'rental-manager'),
    ];
}
