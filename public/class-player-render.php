<?php
/**
 * Renders a single Buzzsprout-style player or, via shortcodes.php,
 * can be used repeatedly for playlist mode.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────
   Helper: make cloud links streamable
   ───────────────────────────────────────────── */
function spp_normalize_audio_url( $url ) {

/* Dropbox → streamable */
if ( strpos( $url, 'dropbox.com' ) !== false ) {
    // If it’s already ?raw=1 we're good
    if ( str_contains( $url, 'raw=1' ) ) return $url;

    // Convert share page to dl.dropboxusercontent
    $url = preg_replace( '#^https://www\\.dropbox\\.com/#', 'https://dl.dropboxusercontent.com/', $url );

    // Strip query and force ?raw=1
    [ $base ] = explode( '?', $url, 2 );
    return $base . '?raw=1';
}



    /* Firebase Storage  → add ?alt=media */
    if ( strpos( $url, 'firebasestorage.googleapis.com' ) !== false &&
         ! str_contains( $url, 'alt=media' ) ) {
        return $url . ( str_contains( $url, '?' ) ? '&' : '?' ) . 'alt=media';
    }

    /* S3 / R2 / Bunny / DO Spaces  → force inline disposition */
    if ( preg_match( '#(s3|r2|bunnycdn|digitaloceanspaces|backblazeb2)\\.#i', $url ) &&
         ! str_contains( $url, 'response-content-disposition' ) ) {
        return $url . ( str_contains( $url, '?' ) ? '&' : '?' )
               . 'response-content-disposition=inline';
    }

    /* Backblaze B2 “download by id” → friendly file path */
    if ( str_contains( $url, 'b2_download_file_by_id' ) ) {
        $url = str_replace( 'b2_download_file_by_id', 'file', $url );
        return preg_replace( '~/file/[0-9a-f]{32}/~', '/file/', $url );
    }

    /* Generic cleanup: strip ?download= / ?dl= flags */
    if ( str_contains( $url, 'download=' ) || str_contains( $url, 'dl=' ) ) {
        $parts = wp_parse_url( $url );
        if ( ! empty( $parts['query'] ) ) {
            parse_str( $parts['query'], $q );
            unset( $q['download'], $q['dl'] );
            $url = $parts['scheme'] . '://' . $parts['host'] . $parts['path'];
            if ( $q ) $url .= '?' . http_build_query( $q );
        }
    }

    return $url;
}

/* ─────────────────────────────────────────────
   Core renderer
   ───────────────────────────────────────────── */
class SPP_Render {

    /**
     * Single player markup for an episode post_id
     */
    public static function single( $post_id ) {

        $post  = get_post( $post_id );
        if ( ! $post || $post->post_type !== 'spp_episode' ) return '';

        $audio = get_post_meta( $post_id, 'spp_audio_url', true );
        if ( ! $audio ) return '<em>Missing audio URL.</em>';

        /* normalize URL for streaming */
        $audio = spp_normalize_audio_url( $audio );

        $title = esc_html( $post->post_title );
        $sub   = esc_html( get_post_meta( $post_id, 'spp_subtitle', true ) ?: get_bloginfo() );
        $art   = get_the_post_thumbnail_url( $post_id, 'medium' )
                 ?: esc_url( get_option( 'spp_default_art', '' ) );

        $id = 'spp_' . wp_generate_password( 6, false );

        /* enqueue front-end assets */
        wp_enqueue_style ( 'spp-style' );
        wp_enqueue_script( 'spp-js'    );

        ob_start(); ?>
        <div class="spp-card" id="<?php echo esc_attr( $id ); ?>" data-audio="<?php echo esc_url( $audio ); ?>">
          <div class="spp-thumb" style="background-image:url('<?php echo $art; ?>')"></div>
          <div class="spp-main">
            <h3 class="spp-title"><?php echo $title; ?></h3>
            <p  class="spp-sub"><?php echo $sub; ?></p>

            <div class="spp-top">
              <button class="spp-play">
  <!-- play icon -->
  <svg class="spp-icon spp-play-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
    <path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.6 0 80L0 432c0 17.4 9.4 33.4 24.5 41.9s33.7 8.1 48.5-.9L361 297c14.3-8.7 23-24.2 23-41s-8.7-32.2-23-41L73 39z"/>
  </svg>
  <!-- pause icon -->
  <svg class="spp-icon spp-pause-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
    <path d="M48 64C21.5 64 0 85.5 0 112V400c0 26.5 21.5 48 48 48h32c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48H48zm192 0c-26.5 0-48 21.5-48 48V400c0 26.5 21.5 48 48 48h32c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48h-32z"/>
  </svg>
</button>

              <div class="spp-wave"></div>
            </div>

            <div class="spp-controls">
              <button data-skip="-15">⟲ 15</button>
              <button data-skip="30">30 ⟳</button>
              <span class="spp-speed">1×</span>
              <span class="spp-time">00:00 | 00:00</span>
              <a   class="spp-download" href="<?php echo esc_url( $audio ); ?>" download>⬇ Download</a>
            </div>
          </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
