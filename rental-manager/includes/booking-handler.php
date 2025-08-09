<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_rntmgr_submit_booking', 'rntmgr_submit_booking');
add_action('wp_ajax_nopriv_rntmgr_submit_booking', 'rntmgr_submit_booking');

function rntmgr_date_overlap_exists($room_id, $checkin, $checkout) {
    global $wpdb;
    $sql = "
        SELECT COUNT(*) FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} m_room ON p.ID = m_room.post_id AND m_room.meta_key = 'room_id' AND m_room.meta_value = %d
        INNER JOIN {$wpdb->postmeta} m_in ON p.ID = m_in.post_id AND m_in.meta_key = 'checkin_date'
        INNER JOIN {$wpdb->postmeta} m_out ON p.ID = m_out.post_id AND m_out.meta_key = 'checkout_date'
        WHERE p.post_type = 'booking'
          AND p.post_status = 'rental_approved'
          AND NOT (%s <= m_in.meta_value OR %s >= m_out.meta_value)
    ";
    $count = $wpdb->get_var($wpdb->prepare($sql, $room_id, $checkout, $checkin));
    return intval($count) > 0;
}

function rntmgr_calculate_totals($room_id, $checkin, $checkout, $coupon_code = '') {
    $price_per_night = floatval(rntmgr_get_room_price($room_id));
    $d1 = new DateTime($checkin);
    $d2 = new DateTime($checkout);
    $nights = max(0, $d1->diff($d2)->days);
    $subtotal = $price_per_night * $nights;

    // Coupon hook
    $discount = apply_filters('rntmgr_coupon_discount', 0.0, $coupon_code, $subtotal, $room_id, $checkin, $checkout);

    $tax_rate = floatval(apply_filters('rntmgr_tax_rate', 0.0, $room_id));
    $tax = max(0, ($subtotal - $discount) * $tax_rate);
    $total = max(0, $subtotal - $discount + $tax);

    return compact('price_per_night', 'nights', 'subtotal', 'discount', 'tax', 'total');
}

function rntmgr_submit_booking() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rntmgr_nonce')) {
        wp_send_json_error(['message' => 'Invalid request.']);
    }

    $room_id  = intval($_POST['room_id'] ?? 0);
    $checkin  = rntmgr_sanitize_date($_POST['checkin'] ?? '');
    $checkout = rntmgr_sanitize_date($_POST['checkout'] ?? '');
    $guests   = max(1, intval($_POST['guests'] ?? 1));
    $name     = sanitize_text_field($_POST['name'] ?? '');
    $email    = sanitize_email($_POST['email'] ?? '');
    $phone    = sanitize_text_field($_POST['phone'] ?? '');
    $coupon   = sanitize_text_field($_POST['coupon'] ?? '');

    if (!$room_id || !$checkin || !$checkout || !is_email($email) || empty($name)) {
        wp_send_json_error(['message' => 'Please fill all required fields correctly.']);
    }
    if (strtotime($checkout) <= strtotime($checkin)) {
        wp_send_json_error(['message' => 'Checkout must be after check-in.']);
    }

    if (rntmgr_date_overlap_exists($room_id, $checkin, $checkout)) {
        wp_send_json_error(['message' => 'Selected dates are not available.']);
    }

    $totals = rntmgr_calculate_totals($room_id, $checkin, $checkout, $coupon);

    $title = sprintf('Booking: %s (%s → %s)', get_the_title($room_id), $checkin, $checkout);
    $booking_id = wp_insert_post([
        'post_type'   => 'booking',
        'post_status' => 'rental_pending',
        'post_title'  => $title,
    ], true);

    if (is_wp_error($booking_id)) {
        wp_send_json_error(['message' => 'Could not create booking.']);
    }

    update_post_meta($booking_id, 'room_id', $room_id);
    update_post_meta($booking_id, 'checkin_date', $checkin);
    update_post_meta($booking_id, 'checkout_date', $checkout);
    update_post_meta($booking_id, 'guests', $guests);
    update_post_meta($booking_id, 'customer_name', $name);
    update_post_meta($booking_id, 'customer_email', $email);
    update_post_meta($booking_id, 'customer_phone', $phone);
    update_post_meta($booking_id, 'coupon_code', $coupon);
    update_post_meta($booking_id, 'subtotal', $totals['subtotal']);
    update_post_meta($booking_id, 'tax', $totals['tax']);
    update_post_meta($booking_id, 'total', $totals['total']);
    update_post_meta($booking_id, 'nights', $totals['nights']);
    update_post_meta($booking_id, 'price_per_night', $totals['price_per_night']);

    // Email notifications
    rntmgr_email_booking_created($booking_id);

    wp_send_json_success([
        'message' => 'Booking submitted! We will confirm shortly.',
        'booking_id' => $booking_id,
        'totals' => $totals
    ]);
}

// Availability API for calendar
add_action('wp_ajax_rntmgr_get_availability', 'rntmgr_get_availability');
add_action('wp_ajax_nopriv_rntmgr_get_availability', 'rntmgr_get_availability');

function rntmgr_get_availability() {
    $room_id = intval($_GET['room_id'] ?? 0);
    if (!$room_id) wp_send_json_success(['ranges' => []]);
    $q = new WP_Query([
        'post_type' => 'booking',
        'post_status' => 'rental_approved',
        'posts_per_page' => -1,
        'meta_query' => [
            ['key' => 'room_id', 'value' => $room_id, 'compare' => '=']
        ],
        'fields' => 'ids'
    ]);

    $ranges = [];
    foreach ($q->posts as $bid) {
        $ranges[] = [
            'checkin' => get_post_meta($bid, 'checkin_date', true),
            'checkout'=> get_post_meta($bid, 'checkout_date', true),
        ];
    }
    wp_send_json_success(['ranges' => $ranges]);
}
