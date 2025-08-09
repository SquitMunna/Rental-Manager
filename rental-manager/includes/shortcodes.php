<?php
if (!defined('ABSPATH')) exit;

// Rooms grid: [rental_rooms]
add_shortcode('rental_rooms', function($atts){
    $atts = shortcode_atts(['per_page' => 12], $atts);
    ob_start();
    include RNTMGR_PATH . 'templates/rooms-grid.php';
    return ob_get_clean();
});

// Booking form: [rental_booking_form room_id="123"]
add_shortcode('rental_booking_form', function($atts){
    $atts = shortcode_atts(['room_id' => 0], $atts);
    $room_id = intval($atts['room_id']);
    if (!$room_id) return '';
    ob_start(); ?>
    <form class="rntmgr-booking-form" data-room="<?php echo esc_attr($room_id); ?>">
        <input type="hidden" name="action" value="rntmgr_submit_booking" />
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('rntmgr_nonce')); ?>" />
        <input type="hidden" name="room_id" value="<?php echo esc_attr($room_id); ?>" />
        <label>Check-in <input type="date" name="checkin" required></label>
        <label>Check-out <input type="date" name="checkout" required></label>
        <label>Guests <input type="number" name="guests" min="1" value="1" required></label>
        <label>Name <input type="text" name="name" required></label>
        <label>Email <input type="email" name="email" required></label>
        <label>Phone <input type="text" name="phone"></label>
        <label>Coupon <input type="text" name="coupon"></label>
        <button type="submit">Book Now</button>
        <div class="rntmgr-msg"></div>
    </form>
    <?php
    return ob_get_clean();
});

// Availability calendar: [rental_availability room_id="123"]
add_shortcode('rental_availability', function($atts){
    $atts = shortcode_atts(['room_id' => 0], $atts);
    $room_id = intval($atts['room_id']);
    if (!$room_id) return '';
    ob_start(); ?>
    <div class="rntmgr-availability" data-room="<?php echo esc_attr($room_id); ?>">
        <div class="rntmgr-calendar"></div>
    </div>
    <?php
    return ob_get_clean();
});

// Use plugin template for single Room
add_filter('template_include', function($template){
    if (is_singular('room')) {
        $plugin_template = RNTMGR_PATH . 'templates/single-room.php';
        if (file_exists($plugin_template)) return $plugin_template;
    }
    return $template;
});
