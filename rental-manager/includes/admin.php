<?php
if (!defined('ABSPATH')) exit;

// Columns for Bookings
add_filter('manage_booking_posts_columns', function($cols){
    $new = [];
    $new['cb'] = $cols['cb'];
    $new['title'] = __('Booking', 'rental-manager');
    $new['room'] = __('Room', 'rental-manager');
    $new['dates'] = __('Dates', 'rental-manager');
    $new['customer'] = __('Customer', 'rental-manager');
    $new['total'] = __('Total', 'rental-manager');
    $new['status'] = __('Status', 'rental-manager');
    return $new;
});

add_action('manage_booking_posts_custom_column', function($col, $post_id){
    if ($col === 'room') {
        $rid = get_post_meta($post_id, 'room_id', true);
        echo $rid ? '<a href="'. esc_url(get_edit_post_link($rid)) .'">'. esc_html(get_the_title($rid)) .'</a>' : '-';
    }
    if ($col === 'dates') {
        $cin = get_post_meta($post_id, 'checkin_date', true);
        $cout = get_post_meta($post_id, 'checkout_date', true);
        echo esc_html("$cin → $cout");
    }
    if ($col === 'customer') {
        $name = get_post_meta($post_id, 'customer_name', true);
        $email = get_post_meta($post_id, 'customer_email', true);
        echo esc_html($name) . '<br/><a href="mailto:'. esc_attr($email) .'">'. esc_html($email) .'</a>';
    }
    if ($col === 'total') {
        $total = get_post_meta($post_id, 'total', true);
        echo esc_html(rntmgr_price_format($total));
    }
    if ($col === 'status') {
        echo esc_html(get_post_status_object(get_post_status($post_id))->label);
    }
}, 10, 2);

// Row actions: Approve / Reject
add_filter('post_row_actions', function($actions, $post){
    if ($post->post_type === 'booking') {
        $approve_url = wp_nonce_url(admin_url('admin-post.php?action=rntmgr_approve&booking_id='.$post->ID), 'rntmgr_booking_action');
        $reject_url  = wp_nonce_url(admin_url('admin-post.php?action=rntmgr_reject&booking_id='.$post->ID), 'rntmgr_booking_action');
        $actions['approve'] = '<a href="'. esc_url($approve_url) .'">'. __('Approve', 'rental-manager') .'</a>';
        $actions['reject']  = '<a href="'. esc_url($reject_url) .'">'. __('Reject', 'rental-manager') .'</a>';
    }
    return $actions;
}, 10, 2);

// Handlers
add_action('admin_post_rntmgr_approve', function(){
    if (!current_user_can('edit_posts') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'rntmgr_booking_action')) wp_die('Not allowed');
    $bid = intval($_GET['booking_id'] ?? 0);
    if ($bid) {
        // Check overlap again before approving
        $rid = intval(get_post_meta($bid, 'room_id', true));
        $cin = get_post_meta($bid, 'checkin_date', true);
        $cout= get_post_meta($bid, 'checkout_date', true);
        if (function_exists('rntmgr_date_overlap_exists') && rntmgr_date_overlap_exists($rid, $cin, $cout)) {
            wp_redirect(add_query_arg(['rntmgr_msg' => 'overlap'], admin_url('edit.php?post_type=booking'))); exit;
        }
        wp_update_post(['ID' => $bid, 'post_status' => 'rental_approved']);
        rntmgr_email_booking_status_changed($bid, 'rental_approved');
    }
    wp_redirect(admin_url('edit.php?post_type=booking')); exit;
});

add_action('admin_post_rntmgr_reject', function(){
    if (!current_user_can('edit_posts') || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'rntmgr_booking_action')) wp_die('Not allowed');
    $bid = intval($_GET['booking_id'] ?? 0);
    if ($bid) {
        wp_update_post(['ID' => $bid, 'post_status' => 'rental_rejected']);
        rntmgr_email_booking_status_changed($bid, 'rental_rejected');
    }
    wp_redirect(admin_url('edit.php?post_type=booking')); exit;
});
add_filter('post_row_actions', function($actions, $post){
    if ($post->post_type === 'booking') {
        $approve_url = wp_nonce_url(admin_url('admin-post.php?action=rntmgr_approve&booking_id='.$post->ID), 'rntmgr_booking_action');
        $reject_url  = wp_nonce_url(admin_url('admin-post.php?action=rntmgr_reject&booking_id='.$post->ID), 'rntmgr_booking_action');
        $actions['approve'] = '<a href="'. esc_url($approve_url) .'">Approve</a>';
        $actions['reject']  = '<a href="'. esc_url($reject_url) .'">Reject</a>';
    }
    return $actions;
}, 10, 2);
