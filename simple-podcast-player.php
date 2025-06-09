<?php
/*
Plugin Name: Simple Podcast Player
Plugin URI:  https://github.com/yourname/simple-podcast-player
Description: Buzzsprout‑style embed with waveform, skip controls, GA4 analytics and featured‑image artwork.
Version: 1.0.1
Author: Delvechio Designs
Author URI: https://delvechiodesigns.com
License: GPL-3.0
*/

defined('ABSPATH')||exit;

/* enqueue */
add_action('wp_enqueue_scripts',function(){
  wp_register_style('spp-style',plugin_dir_url(__FILE__).'assets/player.css',[], '1.0.1');
  wp_register_script('wavesurfer','https://unpkg.com/wavesurfer.js@6.6.2',[],null,true);
  wp_register_script('spp-js',plugin_dir_url(__FILE__).'assets/player.js',['wavesurfer'],'1.0.1',true);

  $ga = get_option('spp_ga4_id','');
  if($ga) wp_localize_script('spp-js','SPP_DATA',['GA_ID'=>$ga]);
});

/* shortcode */
add_shortcode('podcast_player',function($atts){
  global $post;
  $a = shortcode_atts(['audio'=>'','title'=>'','subtitle'=>'','art'=>''], $atts);
  if(!$a['audio']) return '<em>Podcast player: missing audio URL.</em>';

  if(strpos($a['audio'],'dropbox.com')!==false)
     $a['audio']=preg_replace('/\?dl=0$/','?raw=1',$a['audio']);

  $a['title'] = $a['title'] ?: get_the_title();
  $a['subtitle']=$a['subtitle'] ?: get_bloginfo();
  $a['art']=$a['art'] ?: get_the_post_thumbnail_url($post->ID,'medium');

  $id='spp_'+wp_generate_password(6,false);

  wp_enqueue_style('spp-style');
  wp_enqueue_script('spp-js');

?><?php ob_start(); ?>
<div class="spp-card" id="<?php echo esc_attr($id); ?>" data-audio="<?php echo esc_url($a['audio']); ?>">
  <div class="spp-thumb" style="background-image:url('<?php echo esc_url($a['art']); ?>')"></div>
  <div class="spp-main">
    <h3 class="spp-title"><?php echo esc_html($a['title']); ?></h3>
    <p class="spp-sub"><?php echo esc_html($a['subtitle']); ?></p>
    <div class="spp-top"><button class="spp-play">▶</button><div class="spp-wave"></div></div>
    <div class="spp-controls">
      <button data-skip="-15">⟲ 15</button><button data-skip="30">30 ⟳</button>
      <span class="spp-speed">1×</span><span class="spp-time">00:00 | 00:00</span>
      <a class="spp-download" href="<?php echo esc_url($a['audio']); ?>" download>⬇ Download</a>
    </div>
  </div>
</div>
<?php return ob_get_clean(); }); 

/* settings page */
add_action('admin_menu',function(){add_options_page('Podcast Player Settings','Podcast Player','manage_options','spp-settings','spp_settings_page');});
add_action('admin_init',function(){register_setting('spp_opts','spp_ga4_id');});
function spp_settings_page(){?>
<div class="wrap"><h1>Podcast Player Settings</h1>
<form method="post" action="options.php"><?php settings_fields('spp_opts'); ?>
<table class="form-table"><tr><th><label for="spp_ga4_id">GA4 Measurement ID</label></th>
<td><input name="spp_ga4_id" id="spp_ga4_id" value="<?php echo esc_attr(get_option('spp_ga4_id','')); ?>" class="regular-text" placeholder="G-XXXXXXX">
<p class="description">Leave blank if you don’t need GA4 events.</p></td></tr></table><?php submit_button(); ?></form></div>
<?php } ?>
