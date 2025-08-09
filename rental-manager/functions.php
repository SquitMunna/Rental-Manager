// functions.php or in a plugin

add_action('wp_ajax_peaceful_booking_submit', 'peaceful_booking_submit');
add_action('wp_ajax_nopriv_peaceful_booking_submit', 'peaceful_booking_submit');

function peaceful_booking_submit() {
    global $wpdb;

    $table = 'wp_peaceful_bookings'; // Update if your table name differs
    $room_id = intval($_POST['room_id']);
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);

    // Check for overlap
    $conflict = $wpdb->get_var($wpdb->prepare("
        SELECT COUNT(*) FROM $table
        WHERE room_id = %d AND status = 'approved'
        AND NOT (
            %s <= checkin_date OR %s >= checkout_date
        )
    ", $room_id, $checkout, $checkin));

    if ($conflict > 0) {
        wp_send_json_error('❌ Booking conflict found.');
    }

    // Insert new booking
    $wpdb->insert($table, [
        'room_id' => $room_id,
        'checkin_date' => $checkin,
        'checkout_date' => $checkout,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'status' => 'pending'
    ]);

    wp_send_json_success('✅ Booking submitted!');
}
