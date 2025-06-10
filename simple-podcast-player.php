<?php
/*
Plugin Name: Simple Podcast Player
Plugin URI:  https://wordpress.org/plugins/simple-podcast-player
GitHub URI:  https://github.com/delvechio-designs/simple-podcast-player
Description: Buzzsprout-style embed with waveform, skip controls, GA4 analytics, design panel, and featured-image artwork.
Version: 1.1.0
Author: Delvechio Designs
Author URI: https://delvechiodesigns.com
License: GPL-3.0
*/

defined( 'ABSPATH' ) || exit;

/*------------------------------------------------------------------
  1. Enqueue front-end assets + inline CSS variables
-------------------------------------------------------------------*/
add_action( 'wp_enqueue_scripts', function () {

  wp_register_style (
    'spp-style',
    plugin_dir_url( __FILE__ ) . 'assets/player.css',
    [],
    '1.1.0'
  );

  wp_register_script(
    'wavesurfer',
    'https://unpkg.com/wavesurfer.js@6.6.2',
    [],
    null,
    true
  );
  wp_register_script(
    'spp-js',
    plugin_dir_url( __FILE__ ) . 'assets/player.js',
    [ 'wavesurfer' ],
    '1.1.0',
    true
  );

  /* expose GA4 ID if set */
  $ga = get_option( 'spp_ga4_id', '' );
  if ( $ga ) {
      wp_localize_script( 'spp-js', 'SPP_DATA', [ 'GA_ID' => $ga ] );
  }

  /* inject colour variables */
  $vars = sprintf(
    ':root{--spp-accent:%1$s;--spp-wave:%2$s;--spp-progress:%3$s;}',
    esc_attr( get_option('spp_accent'  , '#000') ),
    esc_attr( get_option('spp_wave'    , '#c7c7c7') ),
    esc_attr( get_option('spp_progress', '#1f6bff') )
  );
  wp_add_inline_style( 'spp-style', $vars );
} );

/*------------------------------------------------------------------
  2. Shortcode  [podcast_player]
-------------------------------------------------------------------*/
add_shortcode( 'podcast_player', function ( $atts ) {

  global $post;

  $a = shortcode_atts( [
      'audio'    => '',
      'title'    => '',
      'subtitle' => '',
      'art'      => ''
  ], $atts );

  if ( ! $a['audio'] ) {
      return '<em>Podcast player: missing audio URL.</em>';
  }

  /* make Dropbox links streamable */
  if ( strpos( $a['audio'], 'dropbox.com' ) !== false ) {
      $a['audio'] = preg_replace( '/\\?dl=0$/', '?raw=1', $a['audio'] );
  }

  /* fallbacks */
  $a['title']    = $a['title']    ?: get_the_title();
  $a['subtitle'] = $a['subtitle'] ?: get_bloginfo();
  $a['art']      = $a['art']      ?: get_option( 'spp_default_art',
                          get_the_post_thumbnail_url( $post->ID, 'medium' ) );

  $id = 'spp_' . wp_generate_password( 6, false );

  /* enqueue assets for this page */
  wp_enqueue_style ( 'spp-style' );
  wp_enqueue_script( 'spp-js'    );

  /* render */
  ob_start(); ?>

  <div class="spp-card" id="<?php echo esc_attr( $id ); ?>" data-audio="<?php echo esc_url( $a['audio'] ); ?>">
    <div class="spp-thumb" style="background-image:url('<?php echo esc_url( $a['art'] ); ?>')"></div>
    <div class="spp-main">
      <h3 class="spp-title"><?php echo esc_html( $a['title'] ); ?></h3>
      <p  class="spp-sub"><?php echo esc_html( $a['subtitle'] ); ?></p>

      <div class="spp-top">
        <button class="spp-play">▶</button>
        <div   class="spp-wave"></div>
      </div>

      <div class="spp-controls">
        <button data-skip="-15">⟲ 15</button>
        <button data-skip="30">30 ⟳</button>
        <span class="spp-speed">1×</span>
        <span class="spp-time">00:00 | 00:00</span>
        <a   class="spp-download" href="<?php echo esc_url( $a['audio'] ); ?>" download>⬇ Download</a>
      </div>
    </div>
  </div>

  <?php
  return ob_get_clean();

} );

/*------------------------------------------------------------------
  3. Admin design + GA4 panel
-------------------------------------------------------------------*/
require_once __DIR__ . '/admin-settings.php';

/*------------------------------------------------------------------
  4. Editor toolbar button (Classic + QuickTags)
-------------------------------------------------------------------*/
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
        wp_enqueue_script(
            'spp-editor-shortcode',
            plugins_url( 'admin/editor-shortcode.js', __FILE__ ),
            [ 'quicktags', 'tinymce' ],
            '1.0',
            true
        );
    }
});
