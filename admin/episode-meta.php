<?php
/**
 * Episode meta box + shortcode admin column
 * simple-podcast-player
 */

/* ───────── 1. Meta box UI ───────── */
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'spp_meta',
        'Episode Details',
        'spp_meta_box',
        'spp_episode',
        'normal',
        'high'
    );
} );

function spp_meta_box( $post ) {
    $url = get_post_meta( $post->ID, 'spp_audio_url', true );
    $sub = get_post_meta( $post->ID, 'spp_subtitle', true );
    ?>
    <p>
      <label>MP3 URL<br>
        <input type="url" name="spp_audio_url"
               value="<?php echo esc_attr( $url ); ?>" style="width:100%" required>
      </label>
    </p>
    <p>
      <label>Subtitle<br>
        <input type="text" name="spp_subtitle"
               value="<?php echo esc_attr( $sub ); ?>" style="width:100%">
      </label>
    </p>
    <?php
}

/* ───────── 2. Save meta ───────── */
add_action( 'save_post_spp_episode', function ( $post_id ) {
    if ( isset( $_POST['spp_audio_url'] ) )
        update_post_meta( $post_id, 'spp_audio_url', esc_url_raw( $_POST['spp_audio_url'] ) );

    if ( isset( $_POST['spp_subtitle'] ) )
        update_post_meta( $post_id, 'spp_subtitle', sanitize_text_field( $_POST['spp_subtitle'] ) );
} );



/* ───────── 3. Admin list “Shortcode” column ───────── */
add_filter( 'manage_spp_episode_posts_columns', function ( $cols ) {
    $cols['spp_shortcode'] = 'Shortcode';
    return $cols;
} );

add_action( 'manage_spp_episode_posts_custom_column', function ( $col, $post_id ) {
    if ( $col === 'spp_shortcode' ) {
        echo '<code>[podcast_player id="' . intval( $post_id ) . '"]</code>';
    }
}, 10, 2 );

/* Style the column */
add_action( 'admin_print_styles-edit.php', function () {
    if ( get_current_screen()->post_type !== 'spp_episode' ) return;
    echo '<style>
        .column-spp_shortcode code{
            background:#f6f7f7;padding:2px 6px;border-radius:4px;font-size:12px;
        }
        .column-spp_shortcode{width:220px}
    </style>';
} );
