<?php
if (!defined('ABSPATH')) exit;
get_header();
the_post();
$room_id = get_the_ID();
$price = rntmgr_get_room_price($room_id);
$amen  = (array) get_post_meta($room_id, 'room_amenities', true);
$gallery = get_post_meta($room_id, 'room_gallery_ids', true); $gallery = is_array($gallery) ? $gallery : [];
?>
<main class="rntmgr-room">
    <section class="rntmgr-gallery">
        <?php if (!empty($gallery)) : ?>
            <div class="rntmgr-slider">
                <?php foreach ($gallery as $att_id): ?>
                    <div class="slide"><?php echo wp_get_attachment_image($att_id, 'large'); ?></div>
                <?php endforeach; ?>
            </div>
        <?php elseif (has_post_thumbnail()) : the_post_thumbnail('large'); endif; ?>
    </section>

    <section class="rntmgr-summary">
        <h1><?php the_title(); ?></h1>
        <?php if ($price !== ''): ?>
            <p class="rntmgr-price">Price per night: <?php echo esc_html(rntmgr_price_format($price)); ?></p>
        <?php endif; ?>
        <div class="rntmgr-content"><?php the_content(); ?></div>

        <?php if (!empty($amen)): ?>
            <ul class="rntmgr-amenities">
                <?php foreach ($amen as $a): ?>
                    <li><?php echo esc_html(ucfirst($a)); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="rntmgr-availability-form">
        <h2>Availability</h2>
        <?php echo do_shortcode('[rental_availability room_id="'. $room_id .'"]'); ?>
        <h2>Book this room</h2>
        <?php echo do_shortcode('[rental_booking_form room_id="'. $room_id .'"]'); ?>
    </section>

    <?php $related = rntmgr_get_related_rooms($room_id); if ($related): ?>
    <section class="rntmgr-related">
        <h2>Related Rooms</h2>
        <div class="rntmgr-rooms-grid">
            <?php foreach ($related as $post): setup_postdata($post); ?>
                <article class="rntmgr-room-card">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) the_post_thumbnail('medium'); ?>
                        <h3><?php the_title(); ?></h3>
                    </a>
                </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
