<?php
class SPP_Render {

  static function single( $post_id ) {
    $p      = get_post( $post_id );
    $audio  = get_post_meta( $post_id, 'spp_audio_url', true );
    if ( ! $audio ) return '<em>Missing audio URL.</em>';

    $title  = esc_html( $p->post_title );
    $sub    = esc_html( get_post_meta( $post_id, 'spp_subtitle', true ) );
    $art    = get_the_post_thumbnail_url( $post_id, 'medium' ) ?: esc_url( get_option( 'spp_default_art', '' ) );
    $id     = 'spp_' . wp_generate_password( 6, false );

    wp_enqueue_style ( 'spp-style' );
    wp_enqueue_script( 'spp-js'    );

    ob_start(); ?>
    <div class="spp-card" id="<?php echo $id; ?>" data-audio="<?php echo esc_url( $audio ); ?>">
      <div class="spp-thumb" style="background-image:url('<?php echo $art; ?>')"></div>
      <div class="spp-main">
        <h3 class="spp-title"><?php echo $title; ?></h3>
        <p  class="spp-sub"><?php echo $sub; ?></p>
        <div class="spp-top"><button class="spp-play">▶</button><div class="spp-wave"></div></div>
        <div class="spp-controls">
          <button data-skip="-15">⟲ 15</button><button data-skip="30">30 ⟳</button>
          <span class="spp-speed">1×</span><span class="spp-time">00:00 | 00:00</span>
          <a class="spp-download" href="<?php echo esc_url( $audio ); ?>" download>⬇ Download</a>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
}
