<?php
$pp = intval($atts['per_page'] ?? 12);
$q = new WP_Query(['post_type' => 'room', 'posts_per_page' => $pp]);
?>
<div class="rntmgr-rooms-grid">
<?php while ($q->have_posts()): $q->the_post();
    $room_id = get_the_ID();
    $price = rntmgr_get_room_price($room_id);
    $loc_id = rntmgr_get_room_location_id($room_id);
    $loc = $loc_id ? get_the_title($loc_id) : '';
    ?>
    <article class="rntmgr-room-card">
        <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()) the_post_thumbnail('medium_large'); ?>
            <h3><?php the_title(); ?></h3>
        </a>
        <p class="rntmgr-room-meta">
            <?php if ($loc) echo '<span class="rntmgr-loc">'. esc_html($loc) .'</span>'; ?>
            <?php if ($price !== '') echo '<span class="rntmgr-price">'. esc_html(rntmgr_price_format($price)) .'</span>'; ?>
        </p>
    </article>
<?php endwhile; wp_reset_postdata(); ?>
</div>
