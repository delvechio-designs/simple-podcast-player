<?php
add_shortcode( 'podcast_player', function ( $atts ) {
  return isset( $atts['id'] ) ? SPP_Render::single( intval( $atts['id'] ) ) : '';
});

add_shortcode( 'podcast_playlist', function ( $atts ) {
  $ids = array_map( 'intval', explode( ',', $atts['ids'] ?? '' ) );
  $out = '';
  foreach ( $ids as $i ) $out .= SPP_Render::single( $i );
  return $out;
});
