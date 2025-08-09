add_action('wp_ajax_rntmgr_submit_booking', 'rntmgr_submit_booking');
add_action('wp_ajax_nopriv_rntmgr_submit_booking', 'rntmgr_submit_booking');

function rntmgr_submit_booking() {
    $data = $_POST;

    $post_id = wp_insert_post([
        'post_type' => 'booking',
        'post_title' => sanitize_text_field($data['name']),
        'post_content' => 'Room: ' . $data['room'] . "\nDates: " . $data['checkin'] . ' → ' . $data['checkout'],
        'post_status' => 'publish',
    ]);

    if ($post_id) {
        update_post_meta($post_id, 'email', sanitize_email($data['email']));
        update_post_meta($post_id, 'phone', sanitize_text_field($data['phone']));
        update_post_meta($post_id, 'coupon', sanitize_text_field($data['coupon']));
        wp_send_json_success('Booking submitted!');
    } else {
        wp_send_json_error('Booking failed!');
    }

    wp_die();
}
