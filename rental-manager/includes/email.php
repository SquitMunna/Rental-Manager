<?php
if (!defined('ABSPATH')) exit;

function rntmgr_admin_email() {
    return get_option('admin_email');
}

function rntmgr_email_booking_created($booking_id) {
    $email = get_post_meta($booking_id, 'customer_email', true);
    $name  = get_post_meta($booking_id, 'customer_name', true);
    $room_id = get_post_meta($booking_id, 'room_id', true);
    $cin = get_post_meta($booking_id, 'checkin_date', true);
    $cout= get_post_meta($booking_id, 'checkout_date', true);
    $total = get_post_meta($booking_id, 'total', true);

    $subject_customer = sprintf('We received your booking for %s', get_the_title($room_id));
    $body_customer = sprintf("Hi %s,\n\nThanks for your booking!\nRoom: %s\nDates: %s → %s\nTotal: %s\n\nWe will confirm shortly.",
        $name, get_the_title($room_id), $cin, $cout, rntmgr_price_format($total));

    $subject_admin = sprintf('New booking: %s (%s → %s)', get_the_title($room_id), $cin, $cout);
    $body_admin = sprintf("New booking received.\nCustomer: %s <%s>\nRoom: %s\nDates: %s → %s\nTotal: %s\nBooking ID: #%d",
        $name, $email, get_the_title($room_id), $cin, $cout, rntmgr_price_format($total), $booking_id);

    wp_mail($email, $subject_customer, $body_customer);
    wp_mail(rntmgr_admin_email(), $subject_admin, $body_admin);
}

function rntmgr_email_booking_status_changed($booking_id, $status) {
    $email = get_post_meta($booking_id, 'customer_email', true);
    $room_id = get_post_meta($booking_id, 'room_id', true);
    $cin = get_post_meta($booking_id, 'checkin_date', true);
    $cout= get_post_meta($booking_id, 'checkout_date', true);
    $status_label = rntmgr_booking_statuses()[$status] ?? ucfirst($status);

    $subject = sprintf('Your booking is %s', $status_label);
    $body = sprintf("Hello,\n\nYour booking for %s (%s → %s) is now %s.\n\nThank you.",
        get_the_title($room_id), $cin, $cout, $status_label);

    wp_mail($email, $subject, $body);
}
