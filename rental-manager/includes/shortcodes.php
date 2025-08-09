<?php
if (!defined('ABSPATH')) exit;

function rntmgr_booking_form_shortcode($atts = []) {
    // Ensure our script is enqueued when the form renders
    if (function_exists('rntmgr_enqueue_booking_assets')) {
        rntmgr_enqueue_booking_assets();
    }

    ob_start(); ?>
    <form id="rntmgr-booking-form">
        <div>
            <input type="text" name="name" placeholder="Your Name" required>
        </div>
        <div>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div>
            <input type="text" name="phone" placeholder="Phone" required>
        </div>
        <div>
            <input type="text" name="room" placeholder="Room ID" required>
        </div>
        <div>
            <label>Check-in</label>
            <input type="date" name="checkin" required>
        </div>
        <div>
            <label>Check-out</label>
            <input type="date" name="checkout" required>
        </div>
        <div>
            <input type="text" name="coupon" placeholder="Coupon Code">
        </div>
        <button type="submit">Book Now</button>
    </form>
    <div id="rntmgr-response"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('rntmgr_booking_form', 'rntmgr_booking_form_shortcode');
