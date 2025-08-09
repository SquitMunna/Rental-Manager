<?php
if (!defined('ABSPATH')) exit;

add_action('add_meta_boxes', function() {
    add_meta_box('rntmgr_building_meta', __('Building Details', 'rental-manager'), 'rntmgr_building_meta_cb', 'building', 'normal', 'default');
    add_meta_box('rntmgr_room_meta', __('Room Details', 'rental-manager'), 'rntmgr_room_meta_cb', 'room', 'normal', 'default');
    add_meta_box('rntmgr_booking_meta', __('Booking Details', 'rental-manager'), 'rntmgr_booking_meta_cb', 'booking', 'normal', 'default');
});

function rntmgr_dropdown_posts($post_type, $selected = 0) {
    $items = get_posts(['post_type' => $post_type, 'numberposts' => -1]);
    echo '<select name="' . esc_attr($post_type) . '_select" id="' . esc_attr($post_type) . '_select">';
    echo '<option value="0">'. esc_html__('— Select —', 'rental-manager') .'</option>';
    foreach ($items as $item) {
        printf('<option value="%d" %s>%s</option>',
            $item->ID,
            selected($selected, $item->ID, false),
            esc_html($item->post_title)
        );
    }
    echo '</select>';
}

// Building meta
function rntmgr_building_meta_cb($post) {
    wp_nonce_field('rntmgr_save_meta', 'rntmgr_meta_nonce');
    $location_id = intval(get_post_meta($post->ID, 'building_location_id', true));
    echo '<p><label><strong>'.__('Location', 'rental-manager').'</strong></label><br/>';
    rntmgr_dropdown_posts('location', $location_id);
    echo '</p>';

    $gallery = get_post_meta($post->ID, 'building_gallery_ids', true);
    $gallery = is_array($gallery) ? $gallery : [];
    echo '<p><label><strong>'.__('Gallery (multiple)', 'rental-manager').'</strong></label><br/>';
    echo '<input type="hidden" id="building_gallery_ids" name="building_gallery_ids" value="'. esc_attr(implode(',', $gallery)) .'" />';
    echo '<button class="button" id="building_gallery_btn">'.__('Select Images', 'rental-manager').'</button>';
    echo '<div id="building_gallery_preview"></div>';
}

// Room meta
function rntmgr_room_meta_cb($post) {
    wp_nonce_field('rntmgr_save_meta', 'rntmgr_meta_nonce');
    $location_id = intval(get_post_meta($post->ID, 'room_location_id', true));
    $building_id = intval(get_post_meta($post->ID, 'room_building_id', true));
    $price = get_post_meta($post->ID, 'room_price', true);
    $amenities = (array) get_post_meta($post->ID, 'room_amenities', true);
    $gallery = get_post_meta($post->ID, 'room_gallery_ids', true);
    $gallery = is_array($gallery) ? $gallery : [];

    echo '<p><label><strong>'.__('Location', 'rental-manager').'</strong></label><br/>';
    rntmgr_dropdown_posts('location', $location_id);
    echo '</p>';

    echo '<p><label><strong>'.__('Building', 'rental-manager').'</strong></label><br/>';
    rntmgr_dropdown_posts('building', $building_id);
    echo '</p>';

    echo '<p><label><strong>'.__('Price', 'rental-manager').'</strong></label><br/>';
    echo '<input type="number" step="0.01" name="room_price" value="'. esc_attr($price) .'" /></p>';

    $list = [
        'wifi' => __('Free WiFi', 'rental-manager'),
        'parking' => __('Free Parking', 'rental-manager'),
        'ac' => __('AC', 'rental-manager'),
    ];
    echo '<p><strong>'.__('Amenities', 'rental-manager').'</strong><br/>';
    foreach ($list as $key => $label) {
        printf(
            '<label><input type="checkbox" name="room_amenities[]" value="%s" %s/> %s</label><br/>',
            esc_attr($key),
            in_array($key, $amenities) ? 'checked' : '',
            esc_html($label)
        );
    }
    echo '</p>';

    echo '<p><label><strong>'.__('Gallery (multiple)', 'rental-manager').'</strong></label><br/>';
    echo '<input type="hidden" id="room_gallery_ids" name="room_gallery_ids" value="'. esc_attr(implode(',', $gallery)) .'" />';
    echo '<button class="button" id="room_gallery_btn">'.__('Select Images', 'rental-manager').'</button>';
    echo '<div id="room_gallery_preview"></div>';
}

// Booking meta
function rntmgr_booking_meta_cb($post) {
    wp_nonce_field('rntmgr_save_meta', 'rntmgr_meta_nonce');
    $room_id   = intval(get_post_meta($post->ID, 'room_id', true));
    $checkin   = get_post_meta($post->ID, 'checkin_date', true);
    $checkout  = get_post_meta($post->ID, 'checkout_date', true);
    $name      = get_post_meta($post->ID, 'customer_name', true);
    $email     = get_post_meta($post->ID, 'customer_email', true);
    $phone     = get_post_meta($post->ID, 'customer_phone', true);
    $guests    = get_post_meta($post->ID, 'guests', true);
    $coupon    = get_post_meta($post->ID, 'coupon_code', true);
    $subtotal  = get_post_meta($post->ID, 'subtotal', true);
    $tax       = get_post_meta($post->ID, 'tax', true);
    $total     = get_post_meta($post->ID, 'total', true);

    echo '<p><label><strong>'.__('Room', 'rental-manager').'</strong></label><br/>';
    rntmgr_dropdown_posts('room', $room_id);
    echo '</p>';

    echo '<p><label><strong>'.__('Check-in', 'rental-manager').'</strong></label><br/>';
    echo '<input type="date" name="checkin_date" value="'. esc_attr($checkin) .'" /></p>';

    echo '<p><label><strong>'.__('Check-out', 'rental-manager').'</strong></label><br/>';
    echo '<input type="date" name="checkout_date" value="'. esc_attr($checkout) .'" /></p>';

    echo '<p><label><strong>'.__('Guests', 'rental-manager').'</strong></label><br/>';
    echo '<input type="number" min="1" name="guests" value="'. esc_attr($guests) .'" /></p>';

    echo '<p><label><strong>'.__('Customer Name', 'rental-manager').'</strong></label><br/>';
    echo '<input type="text" name="customer_name" value="'. esc_attr($name) .'" /></p>';

    echo '<p><label><strong>'.__('Customer Email', 'rental-manager').'</strong></label><br/>';
    echo '<input type="email" name="customer_email" value="'. esc_attr($email) .'" /></p>';

    echo '<p><label><strong>'.__('Customer Phone', 'rental-manager').'</strong></label><br/>';
    echo '<input type="text" name="customer_phone" value="'. esc_attr($phone) .'" /></p>';

    echo '<p><label><strong>'.__('Coupon Code', 'rental-manager').'</strong></label><br/>';
    echo '<input type="text" name="coupon_code" value="'. esc_attr($coupon) .'" /></p>';

    echo '<p><label><strong>'.__('Subtotal', 'rental-manager').'</strong></label><br/>';
    echo '<input type="number" step="0.01" name="subtotal" value="'. esc_attr($subtotal) .'" /></p>';

    echo '<p><label><strong>'.__('Tax', 'rental-manager').'</strong></label><br/>';
    echo '<input type="number" step="0.01" name="tax" value="'. esc_attr($tax) .'" /></p>';

    echo '<p><label><strong>'.__('Total', 'rental-manager').'</strong></label><br/>';
    echo '<input type="number" step="0.01" name="total" value="'. esc_attr($total) .'" /></p>';
}

add_action('save_post', function($post_id) {
    if (!isset($_POST['rntmgr_meta_nonce']) || !wp_verify_nonce($_POST['rntmgr_meta_nonce'], 'rntmgr_save_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $post_type = get_post_type($post_id);

    if ($post_type === 'building') {
        if (isset($_POST['location_select'])) {
            update_post_meta($post_id, 'building_location_id', intval($_POST['location_select']));
        }
        if (isset($_POST['building_gallery_ids'])) {
            $ids = array_filter(array_map('intval', explode(',', sanitize_text_field($_POST['building_gallery_ids']))));
            update_post_meta($post_id, 'building_gallery_ids', $ids);
        }
    }

    if ($post_type === 'room') {
        if (isset($_POST['location_select'])) update_post_meta($post_id, 'room_location_id', intval($_POST['location_select']));
        if (isset($_POST['building_select'])) update_post_meta($post_id, 'room_building_id', intval($_POST['building_select']));
        if (isset($_POST['room_price'])) update_post_meta($post_id, 'room_price', floatval($_POST['room_price']));
        $amen = isset($_POST['room_amenities']) ? array_map('sanitize_text_field', (array)$_POST['room_amenities']) : [];
        update_post_meta($post_id, 'room_amenities', $amen);
        if (isset($_POST['room_gallery_ids'])) {
            $ids = array_filter(array_map('intval', explode(',', sanitize_text_field($_POST['room_gallery_ids']))));
            update_post_meta($post_id, 'room_gallery_ids', $ids);
        }
    }

    if ($post_type === 'booking') {
        if (isset($_POST['room_select'])) update_post_meta($post_id, 'room_id', intval($_POST['room_select']));
        if (isset($_POST['checkin_date'])) update_post_meta($post_id, 'checkin_date', rntmgr_sanitize_date($_POST['checkin_date']));
        if (isset($_POST['checkout_date'])) update_post_meta($post_id, 'checkout_date', rntmgr_sanitize_date($_POST['checkout_date']));
        if (isset($_POST['guests'])) update_post_meta($post_id, 'guests', intval($_POST['guests']));
        if (isset($_POST['customer_name'])) update_post_meta($post_id, 'customer_name', sanitize_text_field($_POST['customer_name']));
        if (isset($_POST['customer_email'])) update_post_meta($post_id, 'customer_email', sanitize_email($_POST['customer_email']));
        if (isset($_POST['customer_phone'])) update_post_meta($post_id, 'customer_phone', sanitize_text_field($_POST['customer_phone']));
        if (isset($_POST['coupon_code'])) update_post_meta($post_id, 'coupon_code', sanitize_text_field($_POST['coupon_code']));
        if (isset($_POST['subtotal'])) update_post_meta($post_id, 'subtotal', floatval($_POST['subtotal']));
        if (isset($_POST['tax'])) update_post_meta($post_id, 'tax', floatval($_POST['tax']));
        if (isset($_POST['total'])) update_post_meta($post_id, 'total', floatval($_POST['total']));
    }
});
