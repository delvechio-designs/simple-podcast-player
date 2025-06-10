<?php
/*
Plugin Name: Simple Podcast Player
Plugin URI:  https://wordpress.org/plugins/simple-podcast-player
GitHub URI:  https://github.com/delvechio-designs/simple-podcast-player
Description: Buzzsprout-style player with colour panel, playlist mode, GA4 analytics, and Gutenberg/Classic shortcuts.
Version: 1.2.0
Author: Delvechio Designs
License: GPL-3.0
*/
defined('ABSPATH') || exit;

/* ───── 1. Register CPT: Podcast Episode ───── */
add_action('init', function () {
  register_post_type('spp_episode', [
    'label'         => 'Podcast Episodes',
    'public'        => false,
    'show_ui'       => true,
    'menu_icon'     => 'dashicons-microphone',
    'supports'      => ['title', 'thumbnail'],
    'show_in_rest'  => true,
  ]);
});

/* ───── 2. Global assets ───── */
add_action('wp_enqueue_scripts', function () {
  wp_register_style ('spp-style', plugins_url('assets/css/player.css', __FILE__), [], '1.2');
  wp_register_script('wavesurfer', 'https://unpkg.com/wavesurfer.js@6.6.2', [], null, true);
  wp_register_script('spp-js'   , plugins_url('assets/js/player.js',   __FILE__), ['wavesurfer'], '1.2', true);

  /* inject colour vars */
  $vars = sprintf(
    ':root{--spp-accent:%1$s;--spp-wave:%2$s;--spp-progress:%3$s;--spp-title:%4$s;--spp-sub:%5$s;}',
    esc_attr(get_option('spp_accent','#000')),
    esc_attr(get_option('spp_wave','#c7c7c7')),
    esc_attr(get_option('spp_progress','#1f6bff')),
    esc_attr(get_option('spp_title','#000')),
    esc_attr(get_option('spp_subtitle','#666'))
  );
  wp_add_inline_style('spp-style', $vars);

  $ga = get_option('spp_ga4_id','');
  if ($ga) wp_localize_script('spp-js','SPP_DATA',['GA_ID'=>$ga]);
});

/* ───── 3. Include modules ───── */
require_once __DIR__ . '/admin/settings-panel.php';
require_once __DIR__.'/admin/episode-meta.php';
require_once __DIR__.'/public/class-player-render.php';
require_once __DIR__.'/public/shortcodes.php';

/* ───── 4. Editor button (Classic / TinyMCE) ───── */
add_action('admin_enqueue_scripts', function( $hook ){
  if (in_array($hook,['post.php','post-new.php'])) {
     wp_enqueue_script('spp-editor', plugins_url('admin/editor-shortcode.js', __FILE__), ['quicktags','tinymce'], '1.0', true);
  }
});
