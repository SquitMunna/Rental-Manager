<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_rntmgr_submit_booking', 'rntmgr_submit_booking');
add_action('wp_ajax_nopriv_rntmgr_submit_booking', 'rntmgr_submit_booking');

    // Sanitize incoming fields
    $name     = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email    = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone    = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $room     = isset($_POST['room']) ? sanitize_text_field($_POST['room']) : '';
    $checkin  = isset($_POST['checkin']) ? sanitize_text_field($_POST['checkin']) : '';
    $checkout = isset($_POST['checkout']) ? sanitize_text_field($_POST['checkout']) : '';
    $coupon   = isset($_POST['coupon']) ? sanitize_text_field($_POST['coupon']) : '';

    if (!$name || !$email || !$room || !$checkin || !$checkout) {
        wp_send_json_error(['message' => 'Missing required fields.']);
    }

    // Create Booking post
    $post_id = wp_insert_post([
        'post_type'   => 'booking',
        'post_title'  => $name . ' - ' . $room . ' (' . $checkin . ' → ' . $checkout . ')',
        'post_content'=> "Room: {$room}\nDates: {$checkin} → {$checkout}\nPhone: {$phone}\nCoupon: {$coupon}",
        'post_status' => 'publish',
    ], true);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Booking could not be saved.']);
    }

    // Save meta
    update_post_meta($post_id, 'email', $email);
    update_post_meta($post_id, 'phone', $phone);
    update_post_meta($post_id, 'coupon', $coupon);
    update_post_meta($post_id, 'checkin', $checkin);
    update_post_meta($post_id, 'checkout', $checkout);
    update_post_meta($post_id, 'room', $room);

    wp_send_json_success(['message' => 'Booking submitted! We will confirm shortly.']);
